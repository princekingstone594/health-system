<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AiChat;
use App\Models\AiChatMemory;

class DoctorSummaryController extends Controller
{
    public function generate()
    {
        $patientId = auth()->id();

        // 🧠 Get last conversations
        $messages = AiChat::where('patient_id', $patientId)
            ->latest()
            ->take(15)
            ->get()
            ->reverse()
            ->map(fn ($msg) => "{$msg->role}: {$msg->message}")
            ->implode("\n");

        // 🧠 Get stored memory
        $memories = AiChatMemory::where('patient_id', $patientId)->get();

        $memoryText = $memories->map(function ($m) {
            return "{$m->key}: {$m->value}";
        })->implode(", ");

        // 🤖 AI Summary Request
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "
You are a medical assistant generating a structured report for a doctor.

Create a CLEAN, PROFESSIONAL medical summary.

FORMAT:

Patient Summary:
- Age:
- Allergies:
- Chronic Conditions:
- Medications:

Symptoms Reported:
...

Possible Condition:
...

Recommended Specialty:
...

Urgency Level:
Low / Medium / High

Doctor Notes:
- Key observations
- Risks
- What doctor should check

RULES:
- Be concise and structured
- Do NOT invent missing data
- Use only available info
"
                    ],
                    [
                        'role' => 'user',
                        'content' => "
Patient Known Info:
{$memoryText}

Conversation:
{$messages}
"
                    ]
                ],
                'temperature' => 0.4
            ]);

        $summary = $response['choices'][0]['message']['content'] ?? 'No summary generated';

        return response()->json([
            'summary' => $summary
        ]);
    }
}