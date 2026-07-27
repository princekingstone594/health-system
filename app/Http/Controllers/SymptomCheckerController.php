<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SymptomCheckerController extends Controller
{
    public function index()
    {
        return view('symptoms.index');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'symptoms' => 'required|string|min:5'
        ]);

        $symptoms = $request->symptoms;

        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a medical assistant. Give possible conditions, urgency level (Low, Medium, High), and recommendation. Keep it short.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $symptoms
                    ]
                ],
                'temperature' => 0.7
            ]);

        $result = $response['choices'][0]['message']['content'] ?? 'No response';

        return back()->with('result', $result);
    }
}