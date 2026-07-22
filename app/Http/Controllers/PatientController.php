<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    // List patients
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

    // Show create form
    public function create()
    {
        return view('patients.create');
    }

    // Store patient
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer',
            'status' => 'required|string',
        ]);

        Patient::create($request->all());

        return redirect()->route('patients.index')
            ->with('success', 'Patient added successfully!');
    }

    // Show single patient
    public function show(Patient $patient)
    {
        $appointments = $patient->appointments;

        return view('patients.show', compact('patient', 'appointments'));
    }

    // Edit form
    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    // Update
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

    // Delete
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient deleted successfully');
    }
}