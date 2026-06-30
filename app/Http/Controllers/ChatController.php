<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(User $user = null)
    {
        $me = Auth::id();
        // Fetch all other users as contacts, ordered by online status and name
        $contacts = User::where('id', '!=', $me)
            ->orderByRaw('CASE WHEN last_seen_at >= ? THEN 1 ELSE 0 END DESC', [now()->subMinutes(5)])
            ->orderBy('name')
            ->get();

        $active = $user;
        $messages = collect();
        if ($active) {
            // Verify message access: only messages between Auth user and active user
            $messages = Message::where(function ($q) use ($me, $active) {
                $q->where('sender_id', $me)->where('receiver_id', $active->id);
            })->orWhere(function ($q) use ($me, $active) {
                $q->where('sender_id', $active->id)->where('receiver_id', $me);
            })->orderBy('created_at')->get();

            // Mark unread messages as read
            Message::where('sender_id', $active->id)
                ->where('receiver_id', $me)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('chat.index', compact('contacts', 'active', 'messages'));
    }

    public function send(Request $request, User $user)
    {
        $request->validate(['body' => 'required|string']);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id,
            'body' => $request->body,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'mine' => true,
                    'time' => $message->created_at->format('H:i'),
                ]
            ]);
        }

        return back();
    }

    public function destroy(Message $message)
    {
        // Only sender can delete the message
        abort_unless($message->sender_id === Auth::id(), 403);
        $message->delete();

        if (request()->ajax()) {
            return response()->json(['status' => 'success']);
        }

        return back();
    }

    public function fetch(User $user)
    {
        $me = Auth::id();
        $messages = Message::where(function ($q) use ($me, $user) {
            $q->where('sender_id', $me)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($me, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $me);
        })->orderBy('created_at')->get()->map(fn ($m) => [
            'id' => $m->id,
            'body' => $m->body,
            'sender_id' => $m->sender_id,
            'mine' => $m->sender_id === $me,
            'time' => $m->created_at->format('H:i'),
        ]);

        return response()->json($messages);
    }
}