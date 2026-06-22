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

    public function destroyAll()
    {
        auth()->user()->systemNotifications()->delete();

        return redirect()
            ->route('notifications.index')
            ->with('success', 'Semua notifikasi akaun anda telah dipadam.');
    }
}
