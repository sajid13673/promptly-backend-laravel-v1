<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\Ai\GroqService;
use Exception;
use Illuminate\Http\JsonResponse;

class AIController extends Controller
{
    public function __construct(private GroqService $groqService) {}
    public function generate(GenerateRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $message = $request->input('message', 'Hello AI!');

            $conversation = Conversation::find($request->conversation_id);
            if (!$conversation) {
                $titleResponse = $this->groqService->send([[
                    'role' => 'user',
                    'content' => 'generate a short title for this message : ' . $message
                ]]);

                if ($titleResponse->failed()) {
                    return response()->json([
                        'status' => false,
                        'error' => $titleResponse->json() ?? $titleResponse->body(),
                    ], $titleResponse->status());
                }
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
            $response = $this->groqService->send($messages);

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
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
