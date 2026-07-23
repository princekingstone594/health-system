<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Appointment;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $appointmentData = $request->all();

        $session = Session::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Doctor Appointment',
                    ],
                    'unit_amount' => 5000, // $50.00
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel'),
        ]);

        // Store temp data in session
        session(['booking_data' => $appointmentData]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $data = session('booking_data');

        if (!$data) {
            return redirect('/')->with('error', 'Session expired');
        }

        Appointment::create([
            'doctor_id' => $data['doctor_id'],
            'clinic_id' => $data['clinic_id'],
            'date' => $data['date'],
            'time' => $data['time'],
            'status' => 'confirmed', // ✅ paid
        ]);

        session()->forget('booking_data');

        return redirect('/')->with('success', 'Payment successful! Appointment booked.');
    }

    public function cancel()
    {
        return redirect()->back()->with('error', 'Payment cancelled.');
    }
}