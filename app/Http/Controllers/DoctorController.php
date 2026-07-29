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

    public function calendar()
    {
        $doctorId = auth()->id();

        $appointments = \App\Models\Appointment::where('doctor_id', $doctorId)
            ->get()
            ->map(function ($appt) {
                return[
                    'title' => 'Patient #' . $patient_id,
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

    public function approve($id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->status = 'approved';
        $appointment->save();

        // notify patient
        $appointment->user->notify(new AppointmentUpdated($appointment, 'reschedule'));

        return back()->with('success', 'Appointment approved.');
    }

    public function reject($id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->status = 'rejected';
        $appointment->save();

        // notify patient
        $appointment->user->notify(new AppointmentUpdated($appointment, 'cancel'));

        return back()->with('success', 'Appointment rejected.');
    }

    public function addNotes($id)
    {
        $appointment = Appointment::findOfFail($id);

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
            'doctor_notes'=> 'nullable|string',
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

        $user = $appointment->patient;

        $user->notify(new \App\Notifications\DoctorNotesAdded($appointment));

        return redirect()->back()->with('success', 'Notes saved successfully.');
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

    public function updateMedical(Request $request, $id)
    {
        $appointment = \App\Models\Appointment::findOrFail($id);

        // 🔒 Security check
        if ($appointment->doctor_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'doctor_notes' => 'nullable->doctor_notes',
            'diagnosis' => $request->diagnosis,
            'prescription' => $request->prescription,
            'status' => 'completed', // auto mark done
        ]);

        return back()->with('success', 'Medical record updated.');
    }
}