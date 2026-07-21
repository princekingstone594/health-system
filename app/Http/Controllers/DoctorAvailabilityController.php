<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoctorAvailability;
use Illuminate\Support\Facades\Auth;

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
}