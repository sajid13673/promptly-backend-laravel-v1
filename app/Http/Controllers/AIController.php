<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateRequest;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Exception;

class AIController extends Controller
{
    public function generate(GenerateRequest $request)
    {
        try {
            $user = $request->user();
            $message = $request->input('message', 'Hello AI!');

            $conversation = Conversation::find($request->conversation_id);
            if (!$conversation) {
                $titleResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . config('services.groq.token'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])->post(config('services.groq.url'), [
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => 'generate a short title for this message : ' . $message
                        ]
                    ],
                    "reasoning_effort" => "low",
                    "model" => config('services.groq.model')
                ]);

                if ($titleResponse->failed()) {
                    return response()->json([
                        'status' => false,
                        'error' => $titleResponse->json() ?? $titleResponse->body(),
                    ], $titleResponse->status());
                }
                // $title = $titleResponse->json('text');
                $title = $titleResponse->json('choices.0.message.content');
                $conversation = $user->conversations()->create(["title" => $title]);
            }
            $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get(['role', 'content'])
            ->toArray();
            $messages[] = [
                'role' => 'user',
                'content' => $message,
            ];
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.groq.token'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post(config('services.groq.url'), [
                'messages' => $messages,
                "reasoning_effort" => "low",
                "model" => config('services.groq.model')
            ]);

            if ($response->failed()) {
                return response()->json([
                    'status' => false,
                    'error' => $response->json() ?? $response->body(),
                ], $response->status());
            }

            $reply = $response->json('choices.0.message.content');
            $role = $response->json('choices.0.message.role');
            $conversation->messages()->createMany([
                ['role' => 'user', 'content' => $message],
                ['role' => $role, 'content' => $reply],
            ]);
            $conversation->load('messages');

            return response()->json([
                'status' => true,
                'message' => $message,
                'reply' => $reply,
                'conversation' => $conversation,
                'response' => $response->body()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
