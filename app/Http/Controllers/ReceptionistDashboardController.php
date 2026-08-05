<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

class ReceptionistDashboardController extends Controller
{
    public function index()
    {
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