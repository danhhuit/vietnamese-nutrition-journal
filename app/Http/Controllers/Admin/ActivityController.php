<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ActivityController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('admin.activity', ['logs' => ActivityLog::query()
            ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->integer('user_id')))
            ->latest()->paginate(30)->withQueryString()]);
    }
}
