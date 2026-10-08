<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConversationController extends Controller
{
    private $errMessage = 'Something went wrong, please try again later';
    public function index(Request $request): JsonResponse
    {
        try {
            $conversations = $request->user()->conversations()->orderBy('created_at', 'desc')->get();
            return response()->json(['status' => true, 'data' => $conversations]);
        } catch (\Exception $e) {
            Log::error('Conversation index error : ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => $this->errMessage], 500);
        }
    }
    public function get(int $id): JsonResponse
    {
        try {
            $conversation = Conversation::with('messages')->findOrFail($id);
            return response()->json(['status' => true, 'data' => $conversation]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Conversation not found'], 404);
        } catch (\Exception $e) {
            Log::error('Conversation get error : ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => $this->errMessage], 500);
        }
    }
    public function destroy(int $id): JsonResponse
    {
        try {
            $conversation = Conversation::with('messages')->findOrFail($id);
            $conversation->delete();
            return response()->json(['status' => true, 'message' => 'conversation deleted succesfully']);
        } catch (\Exception $e) {
            Log::error('Conversation delete error : ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => $this->errMessage], 500);
        }
    }
}
