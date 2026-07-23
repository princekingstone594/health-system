<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function index()
    {
        $doctor = auth()->user();

        $schedules = Schedule::with('clinic')
            ->where('doctor_id', $doctor->id)
            ->get();

        return view('schedule.index', compact('schedules'));
    }

    public function create()
    {
        $clinics = auth()-user()->clinics;

        return view('schedules.create', compact('clinics'));
    }

    public function stor(Request $request)
    {
        $request->validate([
            'clinic_id' => 'required|exists:clinics,id',
            'day' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        Schedule::create([
            'doctor_id' => auth()->id(),
            'clinic_id' => $request->clinic_id,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('schedules.index');
    }
}
