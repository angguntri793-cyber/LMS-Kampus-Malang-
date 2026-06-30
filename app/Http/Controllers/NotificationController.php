<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->appNotifications()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    public function show(AppNotification $notification)
    {
        abort_unless($notification->user_id === Auth::id(), 403);
        
        $notification->update(['is_read' => true]);
        
        $data = json_decode($notification->content, true) ?: [];
        
        return view('notifications.show', compact('notification', 'data'));
    }

    public function readAll()
    {
        Auth::user()->appNotifications()->update(['is_read' => true]);
        return back()->with('success', __('Semua notifikasi ditandai telah dibaca.'));
    }
}