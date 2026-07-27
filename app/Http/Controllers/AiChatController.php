<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AiChat;

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

        // 🧠 Get conversation history (last 10 messages)
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

        // 🤖 Call AI
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => array_merge([
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful medical assistant. Ask follow-up questions before giving conclusions. Keep responses safe and short.'
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

        return response()->json([
            'reply' => $reply
        ]);
    }
}