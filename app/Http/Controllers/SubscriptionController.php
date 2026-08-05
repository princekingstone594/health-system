<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class SubscriptionController extends Controller
{
    public function plans()
    {
        return view('subscriptions.plans');
    }

    public function subscribe($plan)
    {
        $user = auth()->user();

        Stripe::setApiKey(config('services.stripe.secret'));

        $priceId = match ($plan) {
            'basic' => env('STRIPE_BASIC_PRICE'),
            'pro' => env('STRIPE_PRO_PRICE'),
            'enterprise' => env('STRIPE_ENTERPRISE_PRICE'),
        };

        $session = Session::create([
            'payment_method_types' => ['card'],

            'mode' => 'subscription',

            'customer_email' => $user->email,

            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],

            'metadata' => [
                'user_id' => $user->id,
                'plan' => $plan,
            ],

            'success_url' => route('subscription.success'),
            'cancel_url' => route('subscription.cancel'),
        ]);

        return redirect($session->url);
    }

    public function success()
    {
        return view('subscriptions.success');
    }

    public function cancel()
    {
        return view('subscriptions.cancel');
    }
}