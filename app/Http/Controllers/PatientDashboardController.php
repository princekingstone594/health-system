<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Patient;

class PatientDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get the patient record linked to this user
        $patient = Patient::where('user_id', $user->id)->first();

        // If no patient profile exists, redirect or handle gracefully
        if (!$patient) {
            return redirect()->route('dashboard')
                ->with('error', 'Patient profile not found.');
        }

        // Upcoming appointments
        $upcomingAppointments = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '>=', now())
            ->with('doctor')
            ->orderBy('appointment_date', 'asc')
            ->take(5)
            ->get();

        // Past appointments
        $pastAppointments = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '<', now())
            ->with('doctor')
            ->orderBy('appointment_date', 'desc')
            ->take(5)
            ->get();

        // Stats
        $totalAppointments = Appointment::where('patient_id', $patient->id)->count();
        $upcomingCount = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '>=', now())
            ->count();

        return view('patient.dashboard', compact(
            'patient',
            'upcomingAppointments',
            'pastAppointments',
            'totalAppointments',
            'upcomingCount'
        ));
    }
}