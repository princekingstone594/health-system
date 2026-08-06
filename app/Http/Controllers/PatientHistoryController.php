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
        $appointments = Appointment::where('user_id', auth()->id())
            ->with(['doctor', 'medicalFiles'])
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        return view('patient.history', compact('appointments'));
    }
}