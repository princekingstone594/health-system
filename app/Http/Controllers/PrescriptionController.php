<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;

class PrescriptionController extends Controller
{
    public function download($id)
    {
        $appointment = Appointment::with(['doctor', 'patient'])->findOrFail($id);

        // 🔐 Optional security
        if (auth()->id() !== $appointment->doctor_id && auth()->id() !== $appointment->user_id) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.prescription', compact('appointment'));

        return $pdf->download('prescription_' . $appointment->id . '.pdf');
    }
}