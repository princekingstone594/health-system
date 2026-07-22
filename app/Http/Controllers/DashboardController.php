<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Services\SmsService;

class DashboardController extends Controller
{
    public function index()
    {
        // =========================
        // STATS
        // =========================
        $patientsCount = Patient::count();
        $doctorsCount = Doctor::count();

        $appointmentsToday = Appointment::whereDate('appointment_date', today())->count();

        $upcomingAppointments = Appointment::whereDate('appointment_date', '>=', today())->count();

        // =========================
        // RECENT DATA
        // =========================
        $recentPatients = Patient::latest()->take(5)->get();

        $recentAppointments = Appointment::with(['patient', 'doctor'])
            ->latest()
            ->paginate(5);

        return view('dashboard', compact(
            'patientsCount',
            'doctorsCount',
            'appointmentsToday',
            'upcomingAppointments',
            'recentPatients',
            'recentAppointments'
        ));
    }

    // =========================
    // TEST SMS
    // =========================
    public function testSms(SmsService $sms)
    {
        $sms->send('+254716435367', 'Test SMS working!');
        return "SMS sent!";
    }
}