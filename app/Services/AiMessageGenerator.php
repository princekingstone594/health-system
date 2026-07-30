<?php

namespace App\Services;

use OpenAI;

class AIMessageGenerator
{
    public function generateFollowUp($diagnosis, $prescription)
    {
        $client = OpenAI::client(config('services.openai.key'));

        $prompt = "
        You are a professional medical assistant.

        Based on:
        Diagnosis: $diagnosis
        Prescription: $prescription

        Write a short, clear follow-up message for the patient.
        Keep it friendly, safe, and easy to understand.
        Avoid complex medical jargon.
        ";

        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful medical assistant.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        return $response->choices[0]->message->content;
    }
}