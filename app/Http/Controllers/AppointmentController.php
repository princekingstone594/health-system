<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\DoctorAvailability;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * List user appointments
     */
    public function index()
    {
        $appointments = Appointment::where('user_id', auth()->id())
            ->with('doctor')
            ->latest()
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show booking page
     */
    public function create(Request $request)
    {
        $doctorId = $request->doctor_id;
        $date = $request->date;

        $slots = [];

        if ($doctorId && $date) {
            $slots = $this->getAvailableSlots($doctorId, $date);
        }

        return view('appointments.create', compact('slots', 'doctorId', 'date'));
    }

    /**
     * Store appointment
     */
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
        ]);

        $doctorId = $request->doctor_id;
        $date = $request->date;
        $time = $request->time;

        // 🚫 Prevent double booking
        $exists = Appointment::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where('time', $time)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Time slot already booked.');
        }

        // ✅ Save appointment (default = pending)
        Appointment::create([
            'user_id' => auth()->id(), // ✅ patient
            'doctor_id' => $doctorId,
            'date' => $date,
            'time' => $time,
            'status' => 'pending',
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment booked. Waiting for doctor approval.');
    }

    /**
     * Generate available slots
     */
    public function getAvailableSlots($doctorId, $date)
    {
        $day = strtolower(Carbon::parse($date)->format('l'));

        // Get doctor's availability
        $availability = DoctorAvailability::where('doctor_id', $doctorId)
            ->where('day_of_week', $day)
            ->first();

        if (!$availability) {
            return [];
        }

        $start = Carbon::parse($availability->start_time);
        $end = Carbon::parse($availability->end_time);
        $duration = $availability->slot_duration;

        $slots = [];

        while ($start < $end) {
            $slots[] = $start->format('H:i');
            $start->addMinutes($duration);
        }

        // 🚫 Remove already booked slots
        $booked = Appointment::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->pluck('time')
            ->toArray();

        return array_values(array_diff($slots, $booked));
    }

    /**
     * Cancel appointment
     */
    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        $appointment->update([
            'status' => 'cancelled'
        ]);

        return back()->with('success', 'Appointment cancelled.');
    }

    /**
     * Show reschedule form
     */
    public function rescheduleForm($id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        return view('appointments.reschedule', compact('appointment'));
    }

    /**
     * Save reschedule
     */
    public function reschedule(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
        ]);

        // 🚫 Prevent double booking
        $exists = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('date', $request->date)
            ->where('time', $request->time)
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Time slot already booked.');
        }

        $appointment->update([
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending', // 🔁 needs re-approval
        ]);

        return redirect()->route('patient.dashboard')
            ->with('success', 'Appointment rescheduled. Awaiting approval.');
    }

    public function slots(Request $request)
    {
        $doctorId = $request->doctor_id;
        $date = $request->date;

        if (!doctorId || !$date) {
            return response()->json([]);
        }

        $slots = $this->getAvailableSlots($doctorId, $date);

        return response()->json($slots);
    }
}