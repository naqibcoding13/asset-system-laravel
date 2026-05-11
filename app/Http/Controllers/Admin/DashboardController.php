<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssetRequest;
use App\Models\RequestItem;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $summary = [
            'total_requests' => AssetRequest::count(),
            'total_assets' => RequestItem::count(),
            'total_budget' => RequestItem::sum('jumlah') ?? 0,
            'total_users' => User::count(),
        ];

        $latestRequests = AssetRequest::with(['user', 'items'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('summary', 'latestRequests'));
    }
}
