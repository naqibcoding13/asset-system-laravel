<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->systemNotifications()->where('status', 'unread')->update(['status' => 'read']);

        return view('notifications.index', [
            'notifications' => $user->systemNotifications()->latest()->get(),
        ]);
    }
}
