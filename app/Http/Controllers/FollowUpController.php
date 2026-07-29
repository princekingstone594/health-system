<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FollowUp;
use App\Service\AiFollowUpService;

class FollowUpController extends Controller
{
    // 📬 Show all follow-ups for patient
    public function index()
    {
        $followUps = FollowUp::where('patient_id', auth()->id())
            ->latest()
            ->get();

        return view('followups.index', compact('followUps'));
    }

    // 💬 Patient reply
    public function reply(Request $request, $id)
    {
        $followUp = FollowUp::findOrFail($id);

        // 🔐 Security check
        if ($followUp->patient_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'response' => 'required|string'
        ]);

        $followUp->update([
            'response' => $request->response,
            'status' => 'answered'
        ]);

        // 🧠 STEP 7 — AI ANALYSIS
        $this->analyzeResponse($followUp);

        return back()->with('success', 'Response submitted.');
    }

    // 🧠 AI RESPONSE ANALYSIS (STEP 7)
    private function analyzeResponse($followUp)
    {
        $text = strtolower($followUp->response);

        // 🚨 Detect risky keywords
        if (
            str_contains($text, 'worse') ||
            str_contains($text, 'pain') ||
            str_contains($text, 'not better') ||
            str_contains($text, 'still sick')
        ) {
            // Flag for doctor attention
            $followUp->update([
                'status' => 'needs_attention'
            ]);
        } else {
            // ✅ Mark safe if no risk detected
            $followUp->update([
                'status' => 'reviewed'
            ]);
        }
    }
}