<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AiChat;
use App\Models\User;

class AiChatController extends Controller
{
    public function index()
    {
        $messages = AiChat::where('user_id', auth()->id())->get();
        return view('ai.chat', compact('messages'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:2'
        ]);

        $userId = auth()->id();
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
            'user_id' => $userId,
            'message' => $request->message,
            'role' => 'user',
            'personality' => $personality
        ]);

        // 🧠 Conversation memory (last 10 messages)
        $history = AiChat::where('user_id', $userId)
            ->latest()
            ->take(10)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->message
                ];
            })
            ->values()
            ->toArray();

        // 🤖 AI request
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => array_merge([
                    [
                        'role' => 'system',
                        'content' => $personalityPrompt . "

You are a medical assistant.

First, ask follow-up questions if symptoms are unclear.

ONLY when reasonably confident, respond in this format:

Condition: ...
Specialty: ...
Urgency: Low/Medium/High
Confidence: Low/Medium/High
Advice: ...

If not confident, DO NOT include Specialty or Condition yet. Just ask questions."
                    ]
                ], $history),
            ]);

        $reply = $response['choices'][0]['message']['content'] ?? 'No response';

        // 💾 Save AI reply
        AiChat::create([
            'user_id' => $userId,
            'message' => $reply,
            'role' => 'assistant',
            'personality' => $personality
        ]);

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
}