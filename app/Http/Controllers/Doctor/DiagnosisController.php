<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AiDiagnosisService;

class DiagnosisController extends Controller
{
    public function generate(Request $request, AiDiagnosisService $ai)
    {
        $request->validate([
            'symptoms' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $result = $ai->generateDiagnosis(
            $request->symptoms,
            $request->notes
        );

        return back()->with('diagnosis', $result);
    }
}