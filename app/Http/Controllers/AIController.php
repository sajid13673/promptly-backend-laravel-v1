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
                    'Authorization' => 'Bearer ' . config('services.cohere.token'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])->post('https://api.cohere.com/v1/chat', [
                    'message' => 'generate a short title for this message : ' . $message
                ]);

                if ($titleResponse->failed()) {
                    return response()->json([
                        'status' => false,
                        'error' => $titleResponse->json() ?? $titleResponse->body(),
                    ], $titleResponse->status());
                }
                $title = $titleResponse->json('text');
                $conversation = $user->conversations()->create(["title" => $title]);
            }

            $history = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get(['role', 'message'])
            ->toArray();
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.cohere.token'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post('https://api.cohere.com/v1/chat', [
                'message' => $message,
                'chat_history' => $history
            ]);

            if ($response->failed()) {
                return response()->json([
                    'status' => false,
                    'error' => $response->json() ?? $response->body(),
                ], $response->status());
            }

            $reply = $response->json('text');
            $conversation->messages()->createMany([
                ['role' => 'USER', 'message' => $message],
                ['role' => 'CHATBOT', 'message' => $reply],
            ]);
            $conversation->load('messages');

            return response()->json([
                'status' => true,
                'message' => $message,
                'reply' => $reply,
                'conversation' => $conversation
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
