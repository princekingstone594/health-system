<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;

class AppointmentController extends Controller
{
    public function create(Patient $patient = null)
    {
        $patients = Patient::all();
        $doctors = Doctor::all();
        $timeslots = $this->getTimeSlots();

        return view('appointments.create', compact('patients', 'doctors', 'timeslots'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        // Check for double booking
        $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'Cancelled')
            ->exists();

        if ($existingAppointment) {
            return back()
                ->withInput()
                ->withErrors(['appointment_time' => 'This time slot is already booked for this doctor.']);
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
        $timeslots = $this->getTimeSlots();

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

        // Check for double booking (excluding current appointment)
        $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('id', '!=', $appointment->id)
            ->where('status', '!=', 'Cancelled')
            ->exists();

        if ($existingAppointment) {
            return back()
                ->withInput()
                ->withErrors(['appointment_time' => 'This time slot is already booked for this doctor.']);
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

        return redirect()->back()->with('success', 'Appointment cancelled successfully!');
    }

    private function getTimeSlots()
    {
        return [
            '09:00',
            '09:30',
            '10:00',
            '10:30',
            '11:00',
            '11:30',
            '14:00',
            '14:30',
            '15:00',
            '15:30',
        ];
    }

    public function getBookedSlots(Request $request)
    {
        $doctorId = $request->doctor_id;
        $date = $request->appointment_date;

        $bookedSlots = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $date)
            ->where('status', '!=', 'Cancelled')
            ->pluck('appointment_time');
        
        return response()->json($bookedSlots);
    }
}
