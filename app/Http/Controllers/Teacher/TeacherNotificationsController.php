<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TeacherNotificationsController extends Controller
{
    /**
     * Display notifications for teacher
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            $emptyNotifications = new LengthAwarePaginator([], 0, 25);
            $emptyNotifications->withPath($request->url());

            return view('teacher.notifications', [
                'notifications' => $emptyNotifications,
                'unreadCount' => 0,
                'readCount' => 0,
            ]);
        }

        $status = $request->get('status', '');
        $type = $request->get('type', '');
        $sort = $request->get('sort', 'latest');
        $perPage = intval($request->get('per_page', 25)) ?: 25;

        // Build query for notifications
        $notificationsQuery = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', 'App\\Models\\User');

        // Filter by status
        if ($status === 'unread') {
            $notificationsQuery->whereNull('read_at');
        } elseif ($status === 'read') {
            $notificationsQuery->whereNotNull('read_at');
        }

        // Filter by type
        if (!empty($type)) {
            $notificationsQuery->where('type', 'like', "%{$type}%");
        }

        // Sort
        if ($sort === 'oldest') {
            $notificationsQuery->orderBy('created_at', 'asc');
        } else {
            $notificationsQuery->orderBy('created_at', 'desc');
        }

        $notifications = $notificationsQuery
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($notification) {
                $data = json_decode($notification->data, true) ?? [];

                $title = $data['title']
                    ?? $data['heading']
                    ?? 'Notification';

                $message = $data['message']
                    ?? $data['body']
                    ?? $data['text']
                    ?? '';

                $type = $data['type']
                    ?? class_basename((string) $notification->type);

                return [
                    'id' => $notification->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'created_at' => \Carbon\Carbon::parse($notification->created_at)->format('M d, Y H:i'),
                    'read_at' => $notification->read_at,
                ];
            });

        // Get counts
        $unreadCount = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', 'App\\Models\\User')
            ->whereNull('read_at')
            ->count();

        $readCount = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', 'App\\Models\\User')
            ->whereNotNull('read_at')
            ->count();

        return view('teacher.notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'readCount' => $readCount,
        ]);
    }
}

