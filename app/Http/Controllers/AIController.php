<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class AIController extends Controller
{
    public function generate(Request $request)
    {
        try {
            $user = $request->user();
            $message = $request->input('message', 'Hello AI!');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.cohere.token'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post('https://api.cohere.com/v1/chat', [
                'message' => $message
            ]);

            if ($response->failed()) {
                return response()->json([
                    'status' => false,
                    'error' => $response->json() ?? $response->body(),
                ], $response->status());
            }
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
            $reply = $response->json('text');
            $conversation->messages()->createMany([
                ['role' => 'USER', 'message' => $message],
                ['role' => 'CHATBOT', 'message' => $reply],
            ]);

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
