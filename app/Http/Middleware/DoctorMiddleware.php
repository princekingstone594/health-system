<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DoctorMiddleware
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

        // 🚫 Not a doctor
        if ($user->role !== 'doctor') {
            return redirect()->route('dashboard')
                ->with('error', 'Access restricted to doctors only.');
        }

        // ✅ Allow access
        return $next($request);
    }
}