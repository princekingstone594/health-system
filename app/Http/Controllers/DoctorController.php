<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\FollowUp;
use Carbon\Carbon;

class DoctorController extends Controller
{
    public function dashboard()
    {
        $doctorId = auth()->id();

        // 📊 Stats
        $totalAppointments = Appointment::where('doctor_id', $doctorId)->count();

        // 📅 Today
        $todayAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', Carbon::today())
            ->orderBy('time')
            ->get();

        $todayCount = $todayAppointments->count();

        // 📆 Upcoming
        $upcomingAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('date', '>', Carbon::today())
            ->orderBy('date')
            ->orderBy('time')
            ->take(10)
            ->get();

        // 🟡 Pending
        $pending = Appointment::where('doctor_id', $doctorId)
            ->where('status', 'pending')
            ->latest()
            ->get();

        // 🔵 Confirmed Today (approved = confirmed)
        $confirmedToday = Appointment::where('doctor_id', $doctorId)
            ->where('status', 'approved')
            ->whereDate('date', Carbon::today())
            ->get();

        // 🟢 Completed
        $completed = Appointment::where('doctor_id', $doctorId)
            ->where('status', 'completed')
            ->latest()
            ->limit(10)
            ->get();

        // 🔴 Cancelled
        $cancelled = Appointment::where('doctor_id', $doctorId)
            ->where('status', 'cancelled')
            ->latest()
            ->limit(10)
            ->get();

        return view('doctor.dashboard', compact(
            'totalAppointments',
            'todayAppointments',
            'todayCount',
            'upcomingAppointments',
            'pending',
            'confirmedToday',
            'completed',
            'cancelled'
        ));
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->doctor_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:pending,approved,completed,cancelled',
        ]);

        $appointment->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Appointment updated.');
    }

    public function calendar()
    {
        $doctorId = auth()->id();

        $appointments = Appointment::where('doctor_id', $doctorId)
            ->get()
            ->map(function ($appt) {
                return [
                    'title' => 'Patient #' . $appt->user_id,
                    'start' => $appt->date . 'T' . $appt->time,
                    'color' => $this->getStatusColor($appt->status),
                ];
            });

        return view('doctor.calendar', compact('appointments'));
    }

    private function getStatusColor($status)
    {
        return match ($status) {
            'approved' => '#16a34a',
            'pending' => '#f59e0b',
            'cancelled' => '#dc2626',
            default => '#3b82f6',
        };
    }

    public function addNotes($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->doctor_id !== auth()->id()) {
            abort(403);
        }

        return view('doctor.notes', compact('appointment'));
    }

    public function storeNotes(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->doctor_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'doctor_notes' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'prescription' => 'nullable|string',
            'is_shared_with_patient' => 'required|boolean'
        ]);

        $appointment->update([
            'doctor_notes' => $request->doctor_notes,
            'diagnosis' => $request->diagnosis,
            'prescription' => $request->prescription,
            'is_shared_with_patient' => $request->is_shared_with_patient,
            'status' => 'completed'
        ]);

        // 🔔 Notify patient
        $appointment->patient->notify(new \App\Notifications\DoctorNotesAdded($appointment));

        // 🤖 Auto follow-up
        FollowUp::create([
            'appointment_id' => $appointment->id,
            'user_id' => $appointment->user_id,
            'message' => $this->generateFollowUpMessage($appointment),
        ]);

        return back()->with('success', 'Notes saved successfully.');
    }

    private function generateFollowUpMessage($appointment)
    {
        return "Hi {$appointment->patient->name}, how are you feeling after your visit? Are your symptoms improving?";
    }

    public function updateProfile(Request $request)
    {
        auth()->user()->update($request->only([
            'specialty',
            'qualifications',
            'location',
            'experience_years'
        ]));

        return back()->with('success', 'Profile updated');
    }
}