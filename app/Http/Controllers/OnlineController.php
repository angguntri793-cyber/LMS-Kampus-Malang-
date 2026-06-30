<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnlineController extends Controller
{
    public function index()
    {
        $online = User::where('last_seen_at', '>=', now()->subMinutes(5))
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();
            
        return view('online.index', compact('online'));
    }

    public function fetchApi()
    {
        $me = Auth::id();
        $onlineUsers = User::where('last_seen_at', '>=', now()->subMinutes(5))
            ->orderBy('name')
            ->get()
            ->map(function ($u) use ($me) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'nomor_induk' => $u->nomor_induk,
                    'role' => $u->role,
                    'is_me' => $u->id === $me,
                    'avatar_url' => $u->avatar 
                        ? asset('storage/' . $u->avatar) 
                        : 'https://ui-avatars.com/api/?name=' . urlencode($u->name),
                    'last_seen' => $u->last_seen_at ? $u->last_seen_at->diffForHumans() : null,
                ];
            });

        return response()->json($onlineUsers);
    }
}