<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Doctor;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'patientsCount' => Patient::count(),
            'appointmentsToday' => Appointment::whereDate('date', today())->count(),
            'doctorsCount' => Doctor::count(),
            'recentPatients' => Patient::latest()->take(5)->get(),
        ]);
    }
}
