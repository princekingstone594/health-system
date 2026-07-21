<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\DoctorAvailability;

class AppointmentController extends Controller
{
    public function create(Patient $patient = null)
    {
        $patients = Patient::all();
        $doctors = Doctor::all();

        $timeslots = [];

        // If doctor selected (optional later via JS)
        if (request()->doctor_id) {
            $doctor = Doctor::find(request()->doctor_id);

            if ($doctor && request()->appointment_date) {

                $day = date('l', strtotime(request()->appointment_date));

                $availability = DoctorAvailability::where('doctor_id', $doctor->id)
                    ->where('day', $day)
                    ->first();

                if ($availability) {
                    $timeslots = $this->generateSlots(
                        $availability->start_time,
                        $availability->end_time,
                        $availability->slot_duration
                    );
                }
            }
        }

        return view('appointments.create', compact('patients', 'doctors', 'timeslots', 'patient'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        // ❗ Double booking protection
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'Cancelled')
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['appointment_time' => 'This time slot is already booked.']);
        }

        Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'Scheduled',
        ]);

        return redirect()->route('dashboard')->with('success', 'Appointment booked successfully!');
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::all();
        $doctors = Doctor::all();

        $timeslots = [];

        $day = date('l', strtotime($appointment->appointment_date));

        $availability = DoctorAvailability::where('doctor_id', $appointment->doctor_id)
            ->where('day', $day)
            ->first();

        if ($availability) {
            $timeslots = $this->generateSlots(
                $availability->start_time,
                $availability->end_time,
                $availability->slot_duration
            );
        }

        return view('appointments.edit', compact('appointment', 'patients', 'doctors', 'timeslots'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        // ❗ Prevent double booking (excluding current)
        $exists = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('id', '!=', $appointment->id)
            ->where('status', '!=', 'Cancelled')
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['appointment_time' => 'This time slot is already booked.']);
        }

        $appointment->update([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
        ]);

        return redirect()->route('dashboard')->with('success', 'Appointment updated successfully!');
    }

    public function cancel(Appointment $appointment)
    {
        $appointment->update(['status' => 'Cancelled']);

        return back()->with('success', 'Appointment cancelled successfully!');
    }

    /**
     * 🔥 Generate slots dynamically
     */
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

    /**
     * 🔥 Used by AJAX (hide booked slots)
     */
    public function getBookedSlots(Request $request)
    {
        $bookedSlots = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('status', '!=', 'Cancelled')
            ->pluck('appointment_time');

        return response()->json($bookedSlots);
    }

    public function ajaxStore(Request $request)
    {
        $request->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        //Prevent double booking
        $exists = \App\Models\Appointment::where('doctor_id', $request->doctor_id)
             ->where('appointment_date', $request->appointment_date)
             ->where('appointment_time', $request->appointment_time)
             ->where('status', '!=', 'Cancelled')
             ->exists();

        if ($exists) {
            return response()->json(['error' => 'Slot already booked'], 422);
        }

        \App\Models\Appointment::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'Scheduled',
        ]);

        return response()->json(['success' => true]);
    }
}