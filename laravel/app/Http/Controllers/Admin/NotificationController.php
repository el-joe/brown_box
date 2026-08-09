<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = auth('admin')->user()->unreadNotifications()
            ->latest()
            ->limit(20)
            ->get(['id', 'data', 'created_at']);

        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications,
        ]);
    }

    public function readAll(): JsonResponse
    {
        auth('admin')->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
