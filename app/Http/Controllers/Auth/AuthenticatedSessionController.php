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
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // Authenticate
        $request->authenticate();

        // Secure session
        $request->session()->regenerate();

        $user = Auth::user();

        // Safety check
        if (!$user || !$user->role) {
            Auth::logout();

            return redirect('/login')->withErrors([
                'email' => 'User role not assigned. Contact admin.',
            ]);
        }

        // Normalize role (🔥 important)
        $role = strtolower($user->role);

        return match ($role) {
            'doctor' => redirect()->route('doctor.dashboard'),
            'receptionist' => redirect()->route('receptionist.dashboard'),
            'patient' => redirect()->route('patient.dashboard'),
            default => abort(403, 'Invalid role'),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}