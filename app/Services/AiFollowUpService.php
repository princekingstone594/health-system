<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiFollowUpService
{
    public function analyze($text)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a medical assistant. Analyze patient response and return JSON with status (stable, concerning, urgent) and advice.'
                ],
                [
                    'role' => 'user',
                    'content' => $text
                ]
            ],
            'temperature' => 0.3,
        ]);

        $content = $response['choices'][0]['message']['content'] ?? '';

        return $this->parse($content);
    }

    private function parse($content)
    {
        $data = json_decode($content, true);

        return [
            'status' => $data['status'] ?? 'unknown',
            'advice' => $data['advice'] ?? null,
        ];
    }
}