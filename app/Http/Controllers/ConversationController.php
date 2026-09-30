<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConversationController extends Controller
{
    private $errMessage = 'Something went wrong, please try again later';
    public function index(Request $request)
    {
        try {
            $conversations = $request->user()->conversations()->orderBy('created_at', 'desc')->get();
            return response()->json(['status' => true, 'data' => $conversations]);
        } catch (\Exception $e) {
            Log::error('Conversation index error : ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => $this->errMessage], 500);
        }
    }
    public function get(int $id)
    {
        try {
            $conversation = Conversation::with('messages')->find($id);
            return response()->json(['status' => true, 'data' => $conversation]);
        } catch (\Exception $e) {
            Log::error('Conversation index error : ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => $this->errMessage], 500);
        }
    }
    public function destroy(int $id)
    {
        try {
            $conversation = Conversation::with('messages')->find($id);
            $conversation->delete();
            return response()->json(['status' => true, 'message' => 'conversation deleted succesfully']);
        } catch (\Exception $e) {
            Log::error('Conversation index error : ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => $this->errMessage], 500);
        }
    }
}
