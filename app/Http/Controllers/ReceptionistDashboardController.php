<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;

class ReceptionistDashboardController extends Controller
{
    public function index()
    {
        $this->authorizeRole('receptionist');

        $patients = User::where('role', 'patient')->latest()->take(5)->get();
        
        // All appointments
        $appointments = Appointment::with(['patient', 'doctor'])
            ->latest()
            ->get();

        // Stats
        $totalAppointments = Appointment::count();

        $todayAppointments = Appointment::whereDate('appointment_date', now())->count();

        $pendingAppointments = Appointment::where('status', 'pending')->count();

        return view('receptionist.dashboard', compact(
            'appointments',
            'totalAppointments',
            'todayAppointments',
            'pendingAppointments'
        ));
    }
}