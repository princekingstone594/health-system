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

    public function analyzePatientResponse(string $respenseText): string
    {
        $prompt = "
        A patient respomded to a medical follow-up:
        
        \"{$responseText}\"

        Classify the response into ONE of these categories:
            - reviewed (patient is okay)
            - needs_attention (patient may be getting worse)
            
        Only return the category word.
        ";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-40-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a medical classifier.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        return trim(strtolower($response['choices'][0]['message']['content']));
    } 
}