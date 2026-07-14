<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request, User $user = null)
    {
        $me = Auth::id();
        $currentUser = Auth::user();
        
        $search = $request->input('search');
        
        $query = User::where('id', '!=', $me)->where('is_active', true);
        
        if ($currentUser->isMahasiswa()) {
            // Students can chat with all admins/lecturers, and students in same prodi or kelas
            $query->where(function($q) use ($currentUser) {
                $q->whereIn('role', ['admin', 'dosen'])
                  ->orWhere(function($sub) use ($currentUser) {
                      $sub->where('role', 'mahasiswa')
                          ->where(function($in) use ($currentUser) {
                              $in->where('prodi_id', $currentUser->prodi_id)
                                 ->orWhere('kelas_id', $currentUser->kelas_id);
                          });
                  });
            });
        }
        
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }
        
        $contacts = $query->orderByRaw('CASE WHEN last_seen_at >= ? THEN 1 ELSE 0 END DESC', [now()->subMinutes(5)])
            ->orderBy('name')
            ->get()
            ->map(function($c) use ($me) {
                $c->unread_count = Message::where('sender_id', $c->id)
                    ->where('receiver_id', $me)
                    ->where('is_read', false)
                    ->count();
                return $c;
            });

        $active = $user;
        $messages = collect();
        
        if ($active) {
            $msgQuery = Message::where(function ($q) use ($me, $active) {
                $q->where('sender_id', $me)
                  ->where('receiver_id', $active->id)
                  ->where('deleted_by_sender', false);
            })->orWhere(function ($q) use ($me, $active) {
                $q->where('sender_id', $active->id)
                  ->where('receiver_id', $me)
                  ->where('deleted_by_receiver', false);
            });

            if ($request->filled('msg_search')) {
                $msgSearch = $request->msg_search;
                $msgQuery->where('body', 'like', "%{$msgSearch}%");
            }

            $messages = $msgQuery->orderBy('created_at')->get();

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

        // Send notification to receiver
        AppNotification::create([
            'user_id' => $user->id,
            'title' => 'Pesan Baru dari ' . Auth::user()->name,
            'content' => json_encode([
                'sender' => Auth::user()->name,
                'body' => substr($message->body, 0, 50),
            ]),
            'type' => 'new_message',
            'url' => '/chat/' . Auth::id(),
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

    public function destroy(Request $request, Message $message)
    {
        $me = Auth::id();
        abort_unless($message->sender_id === $me || $message->receiver_id === $me, 403);
        
        $type = $request->input('type', 'me'); // 'me' or 'everyone'

        if ($type === 'everyone') {
            // Only sender can delete for everyone
            abort_unless($message->sender_id === $me, 403);
            $message->delete();
        } else {
            // Delete for me
            if ($message->sender_id === $me) {
                $message->update(['deleted_by_sender' => true]);
            } else {
                $message->update(['deleted_by_receiver' => true]);
            }
        }

        if ($request->ajax()) {
            return response()->json(['status' => 'success']);
        }

        return back();
    }

    public function fetch(User $user)
    {
        $me = Auth::id();
        
        $messages = Message::where(function ($q) use ($me, $user) {
            $q->where('sender_id', $me)
              ->where('receiver_id', $user->id)
              ->where('deleted_by_sender', false);
        })->orWhere(function ($q) use ($me, $user) {
            $q->where('sender_id', $user->id)
              ->where('receiver_id', $me)
              ->where('deleted_by_receiver', false);
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