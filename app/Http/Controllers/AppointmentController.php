<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\DoctorAvailability;
use Carbon\Carbon;

class AppointmentController extends Controller
{
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
     * Store appointment (BOOKING LOGIC)
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

        // 🚫 STEP 8 — Prevent double booking
        $exists = Appointment::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where('time', $time)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Time slot already booked.');
        }

        // ✅ Save appointment
        Appointment::create([
            'patient_id' => auth()->id(),
            'doctor_id' => $doctorId,
            'date' => $date,
            'time' => $time,
            'status' => 'pending',
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment booked successfully.');
    }

    /**
     * STEP 7 — Generate slots from availability
     * STEP 9 — Remove already booked slots
     */
    public function getAvailableSlots($doctorId, $date)
    {
        $day = strtolower(Carbon::parse($date)->format('l'));

        // Get doctor's availability for that day
        $availability = DoctorAvailability::where('doctor_id', $doctorId)
            ->where('day', $day)
            ->first();

        if (!$availability) {
            return [];
        }

        $start = Carbon::parse($availability->start_time);
        $end = Carbon::parse($availability->end_time);

        $slots = [];

        // ⏱ STEP 7 — Generate 30-min slots
        while ($start < $end) {
            $slots[] = $start->format('H:i');
            $start->addMinutes(30);
        }

        // 📦 STEP 9 — Get booked slots
        $booked = Appointment::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->pluck('time')
            ->toArray();

        // 🚫 Remove booked slots
        $availableSlots = array_filter($slots, function ($slot) use ($booked) {
            return !in_array($slot, $booked);
        });

        return array_values($availableSlots);
    }

    /**
     * List user appointments
     */
    public function index()
    {
        $appointments = Appointment::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('appointments.index', compact('appointments'));
    }

    public function slots(Request  $request)
    {
        $doctorId = $request->doctor_id;
        $date = $request->date;

        if (!$doctorId || !$date) {
            return response()->json([]);
        }

        $slots = $this->getAvailableSlots($doctorId, $date);

        return response()->json($slots);
    }

    public function cancel($id)
    {
        $appointment = Appointment::findOrFail($id);

        // Ensure patient owns it
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        $appointment->status = 'cancelled';
        $appointment->save();

        return back()->with('success', 'Appointment cancelled.');
    }

    public function rescheduleForm($id)
    {
        $appointment = Appointment::findOfFail($id);

        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        return view('appointment.reschedule', compact('appointment'));
    }

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
          return back()->with('error', 'Selected slot already taken.');
       }

       $appointment->date = $request->date;
       $appointment->time = $request->time;
       $appointment->status = 'pending'; // re-approval
       $appointment->save();

       return redirect()->route('patient.dashboard')
          ->with('success', 'Appointment rescheduled.');
   }
}