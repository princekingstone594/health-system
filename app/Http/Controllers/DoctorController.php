<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Carbon\Carbon;

class DoctorController extends Controller
{
    public function dashboard()
    {
        $doctorId = auth()->id();

        // 📅 Today's appointments
        $todayAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', Carbon::today())
            ->orderBy('time')
            ->get();

        // 📆 Upcoming appointments
        $upcomingAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', '>', Carbon::today())
            ->orderBy('date')
            ->orderBy('time')
            ->take(10)
            ->get();

        // 📊 Stats
        $totalAppointments = Appointment::where('doctor_id', $doctorId)->count();

        $todayCount = $todayAppointments->count();

        return view('doctor.dashboard', compact(
            'todayAppointments',
            'upcomingAppointments',
            'totalAppointments',
            'todayCount'
        ));
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        // Ensure doctor owns this appointment
        if ($appointment->doctor_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:approved,cancelled,completed',
        ]);

        $appointment->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Appointment updated.');
    }

    public function calenda()
    {
        $doctorId = auth()->id();

        $appointments = \App\Models\Appointment::where('doctor_id', $doctorId)
            ->get()
            ->map(function ($appt) {
                return[
                    'title' => 'Patient #' . $user_id,
                    'start' => $appt->date . 'T' . $appt->time,
                    'color' => $this->getStatusColor($appt->status),
                ];
            });
        return view('doctor.calendar', compact('appointments'));
    }

    /**
     * Color by status
     */
    private function getStatusColor($status)
    {
        return match ($status) {
            'approved' => '#16a34a', // green
            'pending' => '#f59e0b', // yellow
            'cancelled' => '#dc2626', // red
            'default' => '#3b82f6', // blue
        };
    }
}