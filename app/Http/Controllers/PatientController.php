<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    // Show form
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

        return redirect()->route('dashboard')->with('success', 'Patient added successfully!');
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

        return redirect()->route('dashboard')->with('success', 'Patient updated successfully');

    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('dashboard')->with('success', 'Patient deleted successfully');
    }
}
