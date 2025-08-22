<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $client = $request->client;

        $conversations = Conversation::where('client_id', $client->id)
            ->with(['case:id,case_number,case_title', 'latestMessage'])
            ->withCount('messages')
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('client.chat.index', compact('conversations', 'client'));
    }

    public function show(Request $request, Conversation $conversation)
    {
        $client = $request->client;

        if ($conversation->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        $conversation->markAsRead('client', $client->id);

        return view('client.chat.show', compact('conversation', 'client'));
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $client = $request->client;

        if ($conversation->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'client',
            'sender_id' => $client->id,
            'message' => $request->message,
        ]);

        $conversation->markAsRead('client', $client->id);

        return response()->json(['status' => 'success']);
    }

    public function getMessages(Request $request, Conversation $conversation)
    {
        $client = $request->client;

        if ($conversation->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) use ($client) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_name' => $message->sender_name,
                    'sender_type' => $message->sender_type,
                    'is_own_message' => $message->sender_type === 'client' && $message->sender_id === $client->id,
                    'created_at' => $message->created_at->format('M j, g:i A'),
                    'created_at_iso' => $message->created_at->toISOString(),
                ];
            });

        return response()->json(['messages' => $messages]);
    }
}