<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // ✅ Authenticate user
        $request->authenticate();

        // ✅ Regenerate session (security)
        $request->session()->regenerate();

        $user = Auth::user();

        // ✅ SAFETY CHECK (important)
        if (!$user || !$user->role) {
            Auth::logout();
            return redirect('/login')->withErrors([
                'email' => 'User role not assigned. Contact admin.',
            ]);
        }

        // ✅ CLEAN ROLE-BASED REDIRECT
        return match ($user->role) {
            'doctor' => redirect()->route('doctor.dashboard'),
            'receptionist' => redirect()->route('receptionist.dashboard'),
            'patient' => redirect()->route('patient.dashboard'),
            default => redirect('/dashboard'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}