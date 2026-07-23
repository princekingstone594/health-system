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

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],

            'line_items' => [[
                'price_data' => [
                    'currency' => env('STRIPE_CURRENCY', 'usd'),

                    // 💰 Price (in cents)
                    'unit_amount' => ($appointment->price ?? 50) * 100,

                    'product_data' => [
                        'name' => 'Appointment with Dr. ' . ($appointment->doctor->name ?? 'Doctor'),
                    ],
                ],
                'quantity' => 1,
            ]],

            'mode' => 'payment',

            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel'),
        ]);

        return redirect($session->url);
    }

    /**
     * Payment success
     */
    public function success(Request $request)
    {
        // Optional: update appointment as paid
        // You can use session_id if needed
        $appointment->payment_status = 'paid';
        $appointment->save();

        return view('payment.success');
    }

    /**
     * Payment canceled
     */
    public function cancel()
    {
        return view('payment.cancel');
    }
}