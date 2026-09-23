<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;

class GroqService
{
    public function send(array $messages)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.groq.token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->post(config('services.groq.url'), [
            'messages' => $messages,
            "reasoning_effort" => "low",
            "model" => config('services.groq.model')
        ]);
    }
}
