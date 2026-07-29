<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\FollowUp;

class PatientDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 🔍 Get patient profile linked to user
        $patient = Patient::where('patient_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('dashboard')
                ->with('error', 'Patient profile not found.');
        }

        // 📅 Upcoming appointments
        $upcomingAppointments = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '>=', now())
            ->with('doctor')
            ->orderBy('appointment_date', 'asc')
            ->get();

        // 🕘 Past appointments
        $pastAppointments = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '<', now())
            ->with('doctor')
            ->orderBy('appointment_date', 'desc')
            ->get();

        // 📊 Stats
        $totalAppointments = Appointment::where('patient_id', $patient->id)->count();

        $upcomingCount = Appointment::where('patient_id', $patient->id)
            ->where('appointment_date', '>=', now())
            ->count();

        // 🤖 AI FOLLOW-UPS
        $followUps = FollowUp::where('patient_id', $patient->id)
            ->latest()
            ->get();

        return view('patient.dashboard', compact(
            'patient',
            'upcomingAppointments',
            'pastAppointments',
            'totalAppointments',
            'upcomingCount',
            'followUps'
        ));
    }
}