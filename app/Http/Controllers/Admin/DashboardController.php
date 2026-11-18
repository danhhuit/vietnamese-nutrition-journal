<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dish;
use App\Models\Food;
use App\Models\Restaurant;
use App\Models\SearchLog;
use App\Models\User;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', ['stats' => ['users' => User::count(), 'foods' => Food::count(), 'dishes' => Dish::count(), 'restaurants' => Restaurant::count(), 'searches' => SearchLog::count()], 'popular' => Dish::orderByDesc('viewed_count')->limit(10)->get()]);
    }
}
