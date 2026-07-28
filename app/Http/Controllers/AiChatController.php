<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AiChat;
use App\Models\User;
use App\Models\AiChatMemory;

class AiChatController extends Controller
{
    public function index()
    {
        $messages = AiChat::where('patient_id', auth()->id())->get();
        return view('ai.chat', compact('messages'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:2',
            'personality' => 'nullable|string'
        ]);

        $patientId = auth()->id();
        $personality = $request->personality ?? 'friendly';

        // 🎭 Personality system prompt
        $personalityPrompt = match ($personality) {
            'professional' => "You are a highly professional medical doctor. Be precise, clinical, and concise.",
            'calm' => "You are a calm and reassuring doctor. Speak gently, reduce anxiety, and avoid alarming language.",
            'emergency' => "You are an emergency medical assistant. Be urgent, direct, and prioritize life-threatening risks.",
            default => "You are a friendly and helpful doctor. Speak in a warm, simple, and caring tone."
        };

        // 💾 Save user message
        AiChat::create([
            'patient_id' => $patientId,
            'message' => $request->message,
            'role' => 'user',
            'personality' => $personality
        ]);

        // 🧠 Conversation memory (last 10 messages)
        $history = AiChat::where('patient_id', $patientId)
            ->latest()
            ->take(10)
            ->get()
            ->reverse()
            ->map(fn ($msg) => [
                'role' => $msg->role,
                'content' => $msg->message
            ])
            ->values()
            ->toArray();

        // 🧠 Load stored patient memory
        $memories = AiChatMemory::where('patient_id', $patientId)->get();

        $memoryText = $memories->map(function ($m) {
            return "{$m->key}: {$m->value}";
        })->implode(", ");

        // 🤖 AI Request
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => array_merge([
                    [
                        'role' => 'system',
                        'content' => "
                        {$personalityPrompt}

                        You are simulating a real doctor consultation.

                        Patient Known Info: {$memoryText}

                        RULES:
                        - Ask follow-up questions if symptoms are unclear
                        - Do NOT jump to conclusions too early
                        - Be medically safe and realistic
                        - Speak naturally like a real doctor

                        WHEN READY, respond EXACTLY in format:

                        Condition: ...
                        Specialty: ...
                        Urgency: Low/Medium/High
                        Confidence: Low/Medium/High
                        Advice: ...

                        Also extract and remember if mentioned:
                        - Age
                        - Allergies
                        - Chronic conditions
                        - Medications
                        "
                    ]
                ], $history),
                'temperature' => 0.7
            ]);

        $reply = $response['choices'][0]['message']['content'] ?? 'No response';

        // 💾 Save AI reply
        AiChat::create([
            'patient_id' => $patientId,
            'message' => $reply,
            'role' => 'assistant',
            'personality' => $personality
        ]);

        // 🧠 Store extracted memory
        $this->storeMemory($patientId, $reply);

        // 🧠 Extract specialty
        $specialty = null;
        if (preg_match('/Specialty:\s*(.*)/i', $reply, $matches)) {
            $specialty = trim($matches[1]);
        }

        // 🚨 Extract urgency
        $urgency = null;
        if (preg_match('/Urgency:\s*(.*)/i', $reply, $matches)) {
            $urgency = trim($matches[1]);
        }

        // 👨‍⚕️ Find doctors
        $doctors = [];

        if ($specialty) {
            $doctors = User::where('role', 'doctor')
                ->where('specialty', 'like', "%{$specialty}%")
                ->take(5)
                ->get(['id', 'name', 'specialty', 'location']);
        }

        // 📡 Return response
        return response()->json([
            'reply' => $reply,
            'doctors' => $doctors,
            'urgency' => $urgency
        ]);
    }

    // 🧠 MEMORY EXTRACTION ENGINE
    private function storeMemory($patientId, $text)
    {
        $patterns = [
            'age' => '/age:\s*(\d+)/i',
            'allergies' => '/allerg(?:y|ies):\s*(.*)/i',
            'chronic_condition' => '/chronic condition:\s*(.*)/i',
            'medications' => '/medications?:\s*(.*)/i',
        ];

        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $text, $matches)) {

                AiChatMemory::updateOrCreate(
                    [
                        'patient_id' => $patientId,
                        'key' => $key
                    ],
                    [
                        'value' => trim($matches[1]),
                        'type' => 'medical'
                    ]
                );
            }
        }
    }
}