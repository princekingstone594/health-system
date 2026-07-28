<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

class PatientHistoryController extends Controller
{
    /**
     * 🧾 Show full patient history
     */
    public function index()
    {
        $appointments = Appointment::where('patient_id', auth()->id())
            ->with('doctor')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->get();

        return view('patient.history', compact('appointments'));
    }
}