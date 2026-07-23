<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    /**
     * Create Stripe Checkout Session
     */
    public function checkout($id)
    {
        $appointment = Appointment::findOrFail($id);

        // 🚫 جلوگیری از پرداخت دوباره
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

                // 🔗 Link appointment
                'metadata' => [
                    'appointment_id' => $appointment->id,
                ],

                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel'),
            ]);

            // 💾 Save session ID
            $appointment->stripe_session_id = $session->id;
            $appointment->save();

            return redirect($session->url);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Payment error: ' . $e->getMessage());
        }
    }

    /**
     * Payment success
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect('/')->with('error', 'Missing session ID');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = Session::retrieve($sessionId);

            // ✅ Ensure payment is actually successful
            if ($session->payment_status !== 'paid') {
                return redirect('/')->with('error', 'Payment not completed.');
            }

            $appointmentId = $session->metadata->appointment_id ?? null;

            if (!$appointmentId) {
                return redirect('/')->with('error', 'Appointment not found.');
            }

            $appointment = Appointment::find($appointmentId);

            if ($appointment && $appointment->payment_status !== 'paid') {
                $appointment->payment_status = 'paid';
                $appointment->save();
            }

            return view('payment.success', compact('appointment'));

        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Payment verification failed.');
        }
    }

    /**
     * Payment canceled
     */
    public function cancel()
    {
        return view('payment.cancel');
    }
}