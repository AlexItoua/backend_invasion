<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::where('destinataire_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $notifications
        ]);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:notifications,id'
        ]);

        Notification::whereIn('id', $request->notification_ids)
            ->update(['lu' => true]);

        return response()->json([
            'status' => true,
            'message' => 'Notifications marquées comme lues'
        ]);
    }

    public function unreadCount(Request $request)
    {
        $count = Notification::where('destinataire_id', $request->user()->id)
            ->where('lu', false)
            ->count();

        return response()->json([
            'status' => true,
            'data' => ['count' => $count]
        ]);
    }
}
