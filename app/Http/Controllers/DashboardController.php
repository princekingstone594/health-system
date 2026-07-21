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

        // Recent Patients
        $recentPatients = Patient::latest()->take(5)->get();

        // Recent Appointments
        $recentAppointments = Appointment::with(['patient', 'doctor'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'patientsCount',
            'doctorsCount',
            'appointmentsToday',
            'recentPatients',
            'recentAppointments'
        ));

        $appointments = \App\Models\Appointment::with(['patient', 'doctor'])->latest()->get();

        return view('dashboard', compact('appointments'));
    }

    public function testSms(SmsService $sms)
    {
        $sms->send('+254716435367', 'Test SMS working!');
        return "SMS sent!";
    }
}