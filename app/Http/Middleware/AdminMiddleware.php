<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 🚫 Not logged in
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login first.');
        }

        $user = auth()->user();

        // 🚫 Not an admin
        if ($user->role !== 'admin') {
            return redirect()->route('dashboard')
               ->with('error', 'You are not authorized to access admin panel');
        }

        // ✅ Allow request
        return $next($request);
    }
}