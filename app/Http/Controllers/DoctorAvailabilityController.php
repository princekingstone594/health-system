<?php

namespace App\Http\Controllers;

use App\Models\DoctorAvailability;
use Illuminate\Http\Request;

class DoctorAvailabilityController extends Controller
{
    public function index()
    {
        $availabilities = auth()->user()->availabilities;
        return view('availability.index', compact('availabilities'));
    }

    public function create()
    {
        return view('availability.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'day' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'slot_duration' => 'required|integer',
        ]);

        DoctorAvailability::create([
            'doctor_id' => auth()->id(),
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'slot_duration' => $request->slot_duration,
        ]);

        return redirect()->route('availability.index')->with('success', 'Availability added');
    }
}