<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::where('doctor_id', auth()->user()->doctor->id)->get();
        return view('leaves.index', compact('leaves'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Leave::create([
            'doctor_id' => auth()->user()->doctor->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
        ]);

        return back();
    }

    public function destroy($id)
    {
        Leave::findOrFail($id)->delete();
        return back();
    }
}
