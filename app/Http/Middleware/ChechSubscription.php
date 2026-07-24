<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || !$user->plan) {
            return redirect()->route('plans')
                ->with('error', 'You must subscribe to access this feature.');
        }

        return $next($request);
    }
}