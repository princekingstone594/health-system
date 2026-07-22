<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Services\SmsService;
use App\Models\Appointment;

class DashboardController extends Controller
{
    public function index()
    {
        // Stats
        $patientsCount = Patient::count();
        $doctorsCount = Doctor::count();
        $appointmentsToday = Appointment::whereDate('appointment_date', today())->count();

        // Recent Patients (optional - not used in view yet)
        $recentPatients = Patient::latest()->paginate(5);

        // ✅ FIXED: use paginate instead of get()
        $recentAppointments = Appointment::with(['patient', 'doctor'])
            ->latest()
            ->paginate(5);

        return view('dashboard', compact(
            'patientsCount',
            'doctorsCount',
            'appointmentsToday',
            'recentPatients',
            'recentAppointments'
        ));
    }

    public function testSms(SmsService $sms)
    {
        $sms->send('+254716435367', 'Test SMS working!');
        return "SMS sent!";
    }
}