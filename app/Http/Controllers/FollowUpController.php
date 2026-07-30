<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FollowUp;
use App\Services\AiFollowUpService;
use App\Services\AiMessageGenerator;

class FollowUpController extends Controller
{
    protected $aiFollowUpService;
    protected $aiMessageGenerator;

    public function __construct(
        AiFollowUpService $aiFollowUpService,
        AiMessageGenerator $aiMessageGenerator
    ) {
        $this->aiFollowUpService = $aiFollowUpService;
        $this->aiMessageGenerator = $aiMessageGenerator;
    }

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

        // 🧠 STEP 1: Basic keyword analysis (fast)
        $this->basicAnalysis($followUp);

        // 🤖 STEP 2: AI-powered deep analysis (smart)
        $this->aiAnalysis($followUp);

        return back()->with('success', 'Response submitted.');
    }

    /**
     * ⚡ FAST RULE-BASED ANALYSIS
     */
    private function basicAnalysis($followUp)
    {
        $text = strtolower($followUp->response);

        if (
            str_contains($text, 'worse') ||
            str_contains($text, 'pain') ||
            str_contains($text, 'not better') ||
            str_contains($text, 'still sick')
        ) {
            $followUp->update([
                'status' => 'needs_attention'
            ]);
        } else {
            $followUp->update([
                'status' => 'reviewed'
            ]);
        }
    }

    /**
     * 🤖 AI-POWERED ANALYSIS (SMART LAYER)
     */
    private function aiAnalysis($followUp)
    {
        // Use your domain-specific service
        $analysis = $this->aiFollowUpService->analyzePatientResponse(
            $followUp->question,
            $followUp->response
        );

        // Example expected AI response:
        // [
        //   'severity' => 'low|medium|high',
        //   'summary' => '...',
        //   'needs_attention' => true/false
        // ]

        if (!empty($analysis)) {
            $followUp->update([
                'ai_summary' => $analysis['summary'] ?? null,
                'ai_severity' => $analysis['severity'] ?? null,
                'status' => $analysis['needs_attention']
                    ? 'needs_attention'
                    : $followUp->status
            ]);
        }

        // 🧠 OPTIONAL: Generate suggested doctor reply
        $suggestedReply = $this->aiMessageGenerator->generate([
            'type' => 'doctor_reply',
            'context' => [
                'question' => $followUp->question,
                'response' => $followUp->response,
                'summary' => $analysis['summary'] ?? ''
            ]
        ]);

        if ($suggestedReply) {
            $followUp->update([
                'ai_suggested_reply' => $suggestedReply
            ]);
        }
    }
}