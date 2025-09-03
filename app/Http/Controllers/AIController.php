<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class AIController extends Controller
{
    public function generate(Request $request)
    {
        try {
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

            $data = $response->json();

            return response()->json([
                'status' => true,
                'result' => $data['text'] ?? 'No output',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
