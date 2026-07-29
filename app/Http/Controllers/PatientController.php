<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\FollowUp;

class PatientController extends Controller
{
    /**
     * 👤 PATIENT DASHBOARD (NEW - MAIN FEATURE)
     */
    public function dashboard()
    {
        $patientId = auth()->id();

        $appointments = Appointment::where('patient_id', $patientId)->get();

        $totalAppointments = $appointments->count();

        $upcomingAppointments = $appointments
            ->whereIn('status', ['pending', 'approved'])
            ->sortBy('date');

        $upcomingCount = $upcomingAppointments->count();

        $pastAppointments = $appointments
            ->whereIn('status', ['completed', 'cancelled', 'rejected'])
            ->sortByDesc('date');

        // 🤖 AI FOLLOW-UPS
        $followUps = FollowUp::where('patient_id', $patientId)
            ->latest()
            ->get();

        return view('patient.dashboard', compact(
            'totalAppointments',
            'upcomingAppointments',
            'upcomingCount',
            'pastAppointments',
            'followUps'
        ));
    }

    // ==========================
    // YOUR ORIGINAL CRUD (CLEANED)
    // ==========================

    public function index(Request $request)
    {
        $search = $request->input('search');

        $patients = Patient::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('status', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(5);

        return view('patients.index', compact('patients', 'search'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer',
            'status' => 'required|string',
        ]);

        $patient = Patient::create($request->all());

        // If using AJAX
        if ($request->expectsJson()) {
            return response()->json($patient);
        }

        return redirect()->route('patients.index')
            ->with('success', 'Patient added successfully!');
    }

    public function show(Patient $patient)
    {
        $appointments = $patient->appointments;

        return view('patients.show', compact('patient', 'appointments'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name'=> 'required|string|max:255',
            'age' => 'required|integer',
            'status' => 'required|string',
        ]);

        $patient->update($request->all());

        return redirect()->route('patients.index')
            ->with('success', 'Patient updated successfully');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully');
    }
}