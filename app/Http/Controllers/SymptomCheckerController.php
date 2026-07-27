<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User; // ✅ IMPORTANT (for doctors)

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

        // 🤖 Call OpenAI
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a medical assistant.

Return response in this format:

Condition: ...
Specialty: ...
Urgency: Low/Medium/High
Advice: ...

Keep it short.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $symptoms
                    ]
                ],
                'temperature' => 0.7
            ]);

        $result = $response['choices'][0]['message']['content'] ?? 'No response';

        // 🧠 Extract Specialty from AI response
        $specialty = null;

        if (preg_match('/Specialty:\s*(.*)/i', $result, $matches)) {
            $specialty = trim($matches[1]);
        }

        // 👨‍⚕️ Find matching doctors
        $doctors = [];

        if ($specialty) {
            $doctors = User::where('role', 'doctor')
                ->where('specialty', 'like', "%{$specialty}%")
                ->get();
        }

        // 🔁 Return result + doctors
        return back()
            ->with('result', $result)
            ->with('doctors', $doctors);
    }
}