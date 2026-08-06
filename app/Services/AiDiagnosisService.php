<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;

class AiDiagnosisService
{
    public function generateDiagnosis($symptoms, $notes = null)
    {
        $prompt = "
        You are a medical assistant AI.

        Based on the following symptoms:
        $symptoms

        Additional notes:
        $notes

        Provide:
        1. Possible conditions
        2. Recommended actions
        3. Urgency level (low, medium, high)

        Keep it concise and professional.
        ";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        return $response['choices'][0]['message']['content'];
    }
}