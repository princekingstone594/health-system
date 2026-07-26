<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MedicalRecordController extends Controller
{
    // 📤 Upload form
    public function create($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);

        return view('medical_records.create', compact('appointment'));
    }

    // 📤 Store record
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'title' => 'nullable|string|max:255'
        ]);

        $file = $request->file('file');

        $path = $file->store('medical_records', 'public');

        MedicalRecord::create([
            'patient_id' => Auth::id(),
            'doctor_id' => Appointment::find($request->appointment_id)->doctor_id,
            'appointment_id' => $request->appointment_id,
            'title' => $request->title,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
        ]);

        return redirect()->back()->with('success', 'Record uploaded successfully.');
    }

    // 👀 Patient view own records
    public function index()
    {
        $records = MedicalRecord::where('patient_id', Auth::id())
            ->latest()
            ->get();

        return view('medical_records.index', compact('records'));
    }

    // 👨‍⚕️ Doctor view patient records
    public function doctorView($patientId)
    {
        $records = MedicalRecord::where('patient_id', $patientId)
            ->where('doctor_id', Auth::id())
            ->latest()
            ->get();

        return view('medical_records.doctor_view', compact('records'));
    }

    // 📥 Download
    public function download($id)
    {
        $record = MedicalRecord::findOrFail($id);

        // Security check
        if (
            $record->patient_id !== Auth::id() &&
            $record->doctor_id !== Auth::id()
        ) {
            abort(403);
        }

        return Storage::disk('public')->download($record->file_path);
    }
}