<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    /**
     * Create Checkout Session (Appointment Payment)
     */
    public function checkout($id)
    {
        $appointment = Appointment::findOrFail($id);

        // 🚫 Prevent duplicate payment
        if ($appointment->payment_status === 'paid') {
            return redirect()->back()->with('error', 'This appointment is already paid.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = Session::create([
                'payment_method_types' => ['card'],

                'line_items' => [[
                    'price_data' => [
                        'currency' => env('STRIPE_CURRENCY', 'usd'),
                        'unit_amount' => ($appointment->price ?? 50) * 100,
                        'product_data' => [
                            'name' => 'Appointment with Dr. ' . ($appointment->doctor->name ?? 'Doctor'),
                        ],
                    ],
                    'quantity' => 1,
                ]],

                'mode' => 'payment',

                // 🔗 Tie payment to exact appointment
                'metadata' => [
                    'appointment_id' => $appointment->id,
                ],

                // ⚠️ Do NOT trust success page for payment confirmation
                'success_url' => route('payment.success'),
                'cancel_url' => route('payment.cancel'),
            ]);

            // 💾 Store session ID
            $appointment->stripe_session_id = $session->id;
            $appointment->save();

            return redirect($session->url);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Payment error: ' . $e->getMessage());
        }
    }

    /**
     * Success Page (UI only — no DB updates)
     */
    public function success()
    {
        return view('payment.success');
    }

    /**
     * Cancel Page
     */
    public function cancel()
    {
        return view('payment.cancel');
    }

    /**
     * Stripe Webhook (SOURCE OF TRUTH)
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $secret
            );
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        /**
         * ============================
         * 🎯 HANDLE EVENTS
         * ============================
         */

        // ✅ Payment completed (Appointments + Subscriptions)
        if ($event->type === 'checkout.session.completed') {

            $session = $event->data->object;

            /**
             * 🏥 APPOINTMENT PAYMENT
             */
            $appointmentId = $session->metadata->appointment_id ?? null;

            if ($appointmentId) {
                $appointment = Appointment::find($appointmentId);

                if ($appointment && $appointment->payment_status !== 'paid') {
                    $appointment->payment_status = 'paid';
                    $appointment->save();
                }
            }

            /**
             * 💳 SUBSCRIPTION CREATED (SaaS)
             */
            if ($session->mode === 'subscription') {

                $userId = $session->metadata->user_id ?? null;
                $plan = $session->metadata->plan ?? 'pro';

                if ($userId) {
                    $user = User::find($userId);

                    if ($user) {
                        $user->stripe_customer_id = $session->customer;
                        $user->stripe_subscription_id = $session->subscription;
                        $user->plan = $plan;

                        // ⏳ Temporary (improve later with real Stripe dates)
                        $user->subscription_ends_at = now()->addMonth();

                        $user->save();
                    }
                }
            }
        }

        /**
         * ❌ Subscription canceled (from portal)
         */
        if ($event->type === 'customer.subscription.deleted') {

            $subscription = $event->data->object;

            $user = User::where(
                'stripe_subscription_id',
                $subscription->id
            )->first();

            if ($user) {
                $user->plan = 'free';
                $user->subscription_ends_at = now();
                $user->save();
            }
        }

        /**
         * 🔁 Subscription renewed (monthly payment success)
         */
        if ($event->type === 'invoice.payment_succeeded') {

            $invoice = $event->data->object;

            $user = User::where(
                'stripe_customer_id',
                $invoice->customer
            )->first();

            if ($user && $user->plan !== 'free') {
                $user->subscription_ends_at = now()->addMonth();
                $user->save();
            }
        }

        return response('Webhook handled', 200);
    }
}