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

        // 💾 Save user message
        AiChat::create([
            'user_id' => $userId,
            'message' => $request->message,
            'role' => 'user'
        ]);

        // 🧠 Get last 10 messages (memory)
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

        // 🤖 AI Call (INTELLIGENT PROMPT)
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => array_merge([
                    [
                        'role' => 'system',
                        'content' => 'You are a medical assistant.

First, ask follow-up questions if symptoms are unclear.

ONLY when reasonably confident, respond in this format:

Condition: ...
Specialty: ...
Urgency: Low/Medium/High
Confidence: Low/Medium/High
Advice: ...

If not confident, DO NOT include Specialty or Condition. Just ask questions.'
                    ]
                ], $history),
            ]);

        $reply = $response['choices'][0]['message']['content'] ?? 'No response';

        // 💾 Save AI reply
        AiChat::create([
            'user_id' => $userId,
            'message' => $reply,
            'role' => 'assistant'
        ]);

        // 🧠 Extract intelligence
        $specialty = null;
        $urgency = null;
        $confidence = null;

        if (preg_match('/Specialty:\s*(.*)/i', $reply, $m)) {
            $specialty = trim($m[1]);
        }

        if (preg_match('/Urgency:\s*(.*)/i', $reply, $m)) {
            $urgency = trim($m[1]);
        }

        if (preg_match('/Confidence:\s*(.*)/i', $reply, $m)) {
            $confidence = trim($m[1]);
        }

        // 👨‍⚕️ Smart doctor suggestion (ONLY if HIGH confidence)
        $doctors = [];

        if ($confidence && strtolower($confidence) === 'high' && $specialty) {
            $doctors = User::where('role', 'doctor')
                ->where('specialty', 'like', "%{$specialty}%")
                ->take(5)
                ->get(['id', 'name', 'specialty', 'location']);
        }

        // 📡 Return everything to frontend
        return response()->json([
            'reply' => $reply,
            'doctors' => $doctors,
            'urgency' => $urgency,
            'confidence' => $confidence
        ]);
    }
}