<?php

namespace App\Http\Controllers;

use App\Models\RequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuotationController extends Controller
{
    public function show(Request $request, RequestItem $requestItem)
    {
        $user = $request->user();
        $isOwner = (int) $requestItem->request->user_id === (int) $user->id;
        $isAdmin = $user->role === 'admin';

        abort_unless($isOwner || $isAdmin, 403);

        $path = $requestItem->quotationPath();

        abort_if(! $path || ! Storage::disk('public')->exists($path), 404, 'Fail quotation tidak dijumpai.');

        return Storage::disk('public')->response($path);
    }
}
