<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Appointment;
use App\Mail\AppointmentBooked;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Leave;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // ✅ CREATE (Step 4 integrated)
    public function create(Request $request, Patient $patient = null)
    {
        $patients = Patient::all();
        $doctors = Doctor::all();

        return view('appointments.create', [
            'patients' => $patients,
            'doctors' => $doctors,
            'patient' => $patient,
            'doctor_id' => $request->doctor_id,
            'date' => $request->date,
            'time' => $request->time,
        ]);
    }

    // ✅ STORE (Step 5 integrated + fixed)
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        // 🚫 LEAVE BLOCK
        $onLeave = Leave::where('doctor_id', $request->doctor_id)
            ->whereDate('start_date', '<=', $request->appointment_date)
            ->whereDate('end_date', '>=', $request->appointment_date)
            ->exists();

        if ($onLeave) {
            return back()->withErrors([
                'appointment_date' => 'Doctor is on leave this day.'
            ]);
        }

        // ❗ DOUBLE BOOKING PROTECTION (Step 5)
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'Cancelled')
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'appointment_time' => 'This time slot is already booked.'
            ]);
        }

        // ✅ CREATE ONCE (FIXED)
        $appointment = Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'Scheduled',
        ]);

        // 📧 SEND EMAIL (FIXED POSITION)
        Mail::to($appointment->patient->email)
            ->send(new AppointmentBooked($appointment));

        return redirect()->route('dashboard')
            ->with('success', 'Appointment booked successfully!');
    }

    // ✅ EDIT
    public function edit(Appointment $appointment)
    {
        $patients = Patient::all();
        $doctors = Doctor::all();

        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    // ✅ UPDATE
    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        // 🚫 LEAVE BLOCK
        $onLeave = Leave::where('doctor_id', $request->doctor_id)
            ->whereDate('start_date', '<=', $request->appointment_date)
            ->whereDate('end_date', '>=', $request->appointment_date)
            ->exists();

        if ($onLeave) {
            return back()->withErrors([
                'appointment_date' => 'Doctor is on leave this day.'
            ]);
        }

        // ❗ DOUBLE BOOKING
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('id', '!=', $appointment->id)
            ->where('status', '!=', 'Cancelled')
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'appointment_time' => 'This time slot is already booked.'
            ]);
        }

        $appointment->update($request->only([
            'patient_id',
            'doctor_id',
            'appointment_date',
            'appointment_time'
        ]));

        return redirect()->route('dashboard')
            ->with('success', 'Appointment updated successfully!');
    }

    // ✅ CANCEL
    public function cancel(Appointment $appointment)
    {
        $appointment->update(['status' => 'Cancelled']);

        return back()->with('success', 'Appointment cancelled successfully!');
    }

    // 🔧 SLOT GENERATOR
    private function generateSlots($start, $end, $duration)
    {
        $slots = [];
        $startTime = strtotime($start);
        $endTime = strtotime($end);

        while ($startTime < $endTime) {
            $slotEnd = strtotime("+{$duration} minutes", $startTime);
            if ($slotEnd > $endTime) break;

            $slots[] = date('H:i', $startTime);
            $startTime = $slotEnd;
        }

        return $slots;
    }

    // 🔥 SMART BOOKING PAGE
    public function booking()
    {
        $doctors = Doctor::all();

        return view('appointments.booking', compact('doctors'));
    }

    // 🔥 AVAILABLE SLOTS (FIXED VERSION)
    public function getAvailableSlots(Request $request)
    {
        $doctorId = $request->doctor_id;
        $date = $request->date;

        $day = strtolower(Carbon::parse($date)->format('l'));

        // ✅ FIXED QUERY
        $availability = DoctorAvailability::where('doctor_id', $doctorId)
            ->where('day', $day)
            ->first();

        if (!$availability) {
            return response()->json([]);
        }

        $start = Carbon::parse($availability->start_time);
        $end = Carbon::parse($availability->end_time);

        $slots = [];

        while ($start < $end) {
            $slots[] = $start->format('H:i');
            $start->addMinutes(30);
        }

        // 🚫 REMOVE BOOKED
        $booked = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'Cancelled')
            ->pluck('appointment_time')
            ->toArray();

        $available = array_values(array_diff($slots, $booked));

        return response()->json($available);
    }
}