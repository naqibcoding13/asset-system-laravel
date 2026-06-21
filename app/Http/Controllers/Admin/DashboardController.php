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
        $currentYear = (string) now()->year;
        $currentYearRequestIds = AssetRequest::query()
            ->active()
            ->where('tahun', $currentYear)
            ->pluck('id');

        $summary = [
            'total_requests' => $currentYearRequestIds->count(),
            'total_assets' => RequestItem::whereIn('request_id', $currentYearRequestIds)->count(),
            'total_budget' => RequestItem::whereIn('request_id', $currentYearRequestIds)->sum('jumlah') ?? 0,
            'total_users' => User::count(),
            'current_year' => $currentYear,
        ];

        $latestRequests = AssetRequest::active()->with(['user', 'items'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('summary', 'latestRequests'));
    }
}
