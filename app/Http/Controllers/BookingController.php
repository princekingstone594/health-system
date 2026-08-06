<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Schedule;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentBooked;

class BookingController extends Controller
{
    // Show booking page
    public function show(User $doctor)
    {
        // FIX: correct relationship name
        $clinics = $doctor->clinics ?? [];

        return view('booking.show', compact('doctor', 'clinics'));
    }

    // Get available slots (AJAX)
    public function slots(Request $request)
    {
        $doctorId = $request->doctor_id;
        $clinicId = $request->clinic_id;
        $date = Carbon::parse($request->date);

        // FIX: correct day format
        $day = $date->format('l'); // Monday, Tuesday...

        // Get schedule
        $schedule = Schedule::where('doctor_id', $doctorId)
            ->where('clinic_id', $clinicId)
            ->where('day', $day)
            ->first();

        if (!$schedule) {
            return response()->json([]);
        }

        // Generate slots (30 mins)
        $slots = [];
        $start = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);

        while ($start < $end) {
            $slots[] = $start->format('H:i');
            $start->addMinutes(30);
        }

        // Remove booked slots
        $booked = Appointment::where('doctor_id', $doctorId)
            ->where('clinic_id', $clinicId)
            ->whereDate('date', $date)
            ->pluck('time')
            ->toArray();

        $available = array_values(array_diff($slots, $booked));

        return response()->json($available);
    }

    // Store booking
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required',
            'clinic_id' => 'required',
            'date' => 'required',
            'time' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
        ]);

        // FIX: create appointment first
        $appointment = Appointment::create([
            'doctor_id' => $request->doctor_id,
            'clinic_id' => $request->clinic_id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending',
        ]);

        // Send email
        Mail::to($request->email)->send(new AppointmentBooked($appointment));

        return back()->with('success', 'Appointment booked!');
    }
}