<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoctorAvailability;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Appointment;

class DoctorAvailabilityController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->doctor;

        $availabilities = DoctorAvailability::where('doctor_id', $doctor->id)
            ->get()
            ->keyBy('day_of_week');

        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

        return view('availability.index', compact('availabilities', 'days'));
    }

    public function store(Request $request)
    {
        $doctor = Auth::user()->doctor;

        foreach ($request->days as $day => $data) {

            DoctorAvailability::updateOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                ],
                [
                    'start_time' => $data['start_time'] ?? null,
                    'end_time' => $data['end_time'] ?? null,
                    'slot_duration' => $data['slot_duration'] ?? 30,
                    'is_active' => isset($data['active']),
                ]
            );
        }

        return back()->with('success', 'Availability updated!');
    }

    public function calendar(Request $request)
    {
        $doctor = auth()->user()->doctor;

        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $start = \Carbon\Carbon::create($year, $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $appointments = \App\Models\Appointment::where('doctor_id', $doctor->id)
            ->whereBetween('appointment_date', [$start, $end])
            ->where('status', '!=', 'Cancelled')
            ->get()
            ->groupBy('appointment_date');

        $availabilities = \App\Models\DoctorAvailability::where('doctor_id', $doctor->id)
           ->get()
           ->keyBy('day_of_week');

        $leaves = Leave::where('doctor_id', $doctorId)->get();

        return view('availability.calendar', compact(
            'start', 'end', 'appointments', 'availabilities', 'month', 'year', 'leaves'
       ));
    }
}