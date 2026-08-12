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
        $this->authorizeRole('doctor');
        
        $doctorId = auth()->id();

        // 📊 TOTAL APPOINTMENTS (for this doctor)
        $totalAppointments = Appointment::where('doctor_id', $doctorId)->count();

        // 📅 TODAY'S DATE
        $today = Carbon::today()->toDateString();

        // 📅 TODAY'S APPOINTMENTS
        $todayAppointments = Appointment::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $today)
            ->orderBy('time', 'asc')
            ->get();

        $todayCount = $todayAppointments->count();

        // 📆 UPCOMING APPOINTMENTS (after today)
        $upcomingAppointments = Appointment::with('patient')
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', '>', $today)
            ->orderBy('appointment_date', 'asc')
            ->orderBy('time', 'asc')
            ->limit(10)
            ->get();

        // 🤖 AI FOLLOW-UPS (latest ones for this doctor's patients)
        $followUps = FollowUp::with('patient')
            ->whereIn('user_id', Appointment::where('doctor_id', $doctorId)->pluck('user_id'))
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