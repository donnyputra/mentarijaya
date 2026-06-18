<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function readAll(Request $request)
    {
        if (!Schema::hasTable('notifications')) {
            return response()->json(['status' => 'notifications_table_missing'], 409);
        }

        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'status' => 'ok',
            'unread_count' => Auth::user()->fresh()->unreadNotifications()->count(),
        ]);
    }

    public function index(Request $request)
    {
        if (!Schema::hasTable('notifications')) {
            return response()->json(['status' => 'notifications_table_missing'], 409);
        }

        $limit = max(1, min((int) $request->get('limit', 10), 50));
        $offset = max((int) $request->get('offset', 0), 0);

        $notifications = Auth::user()
            ->notifications()
            ->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END ASC')
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit + 1)
            ->get();

        $hasMore = $notifications->count() > $limit;
        $notifications = $notifications->take($limit)->values();

        return response()->json([
            'status' => 'ok',
            'unread_count' => Auth::user()->unreadNotifications()->count(),
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? __('Notification'),
                    'message' => $notification->data['message'] ?? '',
                    'url' => $notification->data['url'] ?? '#',
                    'is_read' => $notification->read_at !== null,
                    'created_at_human' => optional($notification->created_at)->diffForHumans(),
                ];
            }),
            'has_more' => $hasMore,
            'next_offset' => $offset + $notifications->count(),
        ]);
    }
}
