<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $conversations = $request->user()->conversations;
            return response()->json(['status' => true, 'data' => $conversations]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    public function get($id) {
        try {
            $conversation = Conversation::with('messages')->find($id);
            return response()->json(['status' => true, 'data' => $conversation]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
