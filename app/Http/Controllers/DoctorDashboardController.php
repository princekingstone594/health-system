<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\FollowUp;
use Carbon\Carbon;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        $doctorId = auth()->id();

        // 📊 TOTAL APPOINTMENTS (for this doctor)
        $totalAppointments = Appointment::where('doctor_id', $doctorId)->count();

        // 📅 TODAY'S DATE
        $today = Carbon::today()->toDateString();

        // 📅 TODAY'S APPOINTMENTS
        $todayAppointments = Appointment::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereDate('date', $today)
            ->orderBy('time', 'asc')
            ->get();

        $todayCount = $todayAppointments->count();

        // 📆 UPCOMING APPOINTMENTS (after today)
        $upcomingAppointments = Appointment::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereDate('date', '>', $today)
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->limit(10)
            ->get();

        // 🤖 AI FOLLOW-UPS (latest ones related to this doctor)
        $followUps = FollowUp::with('patient')
            ->where('doctor_id', $doctorId)
            ->latest()
            ->limit(10)
            ->get();

        return view('doctor.dashboard', compact(
            'totalAppointments',
            'todayAppointments',
            'todayCount',
            'upcomingAppointments',
            'followUps'
        ));
    }
}