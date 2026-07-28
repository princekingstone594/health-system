<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalFile;

class MedicalFileController extends Controller
{
    /**
     * Upload file
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'appointment_id' => 'required|exists:appointments,id'
        ]);

        $path = $request->file('file')->store('medical_files', 'public');

        MedicalFile::create([
            'appointment_id' => $request->appointment_id,
            'patient_id' => auth()->id(),
            'file_path' => $path,
            'original_name' => $request->file('file')->getClientOriginalName(),
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }

    /**
     * Download file
     */
    public function download($id)
    {
        $file = MedicalFile::findOrFail($id);

        return response()->download(storage_path('app/public/' . $file->file_path), $file->original_name);
    }

    /**
     * Delete file
     */
    public function destroy($id)
    {
        $file = MedicalFile::findOrFail($id);

        if ($file->patient_id !== auth()->id()) {
            abort(403);
        }

        unlink(storage_path('app/public/' . $file->file_path));

        $file->delete();

        return back()->with('success', 'File deleted.');
    }
}