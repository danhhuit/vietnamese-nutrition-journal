<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Food;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class FavoriteController extends Controller
{
    private const TYPES = ['dish' => Dish::class, 'food' => Food::class, 'restaurant' => Restaurant::class];

    public function index(Request $request): View
    {
        return view('favorites.index', ['favorites' => $request->user()->favorites()->with('favoritable')->latest()->get()]);
    }

    public function toggle(Request $request, string $type, int $id): RedirectResponse
    {
        abort_unless(isset(self::TYPES[$type]), 404);
        $model = self::TYPES[$type]::findOrFail($id);
        $favorite = $request->user()->favorites()->whereMorphedTo('favoritable', $model)->first();
        $favorite ? $favorite->delete() : $request->user()->favorites()->create(['favoritable_type' => $model->getMorphClass(), 'favoritable_id' => $model->id]);

        return back()->with('status', $favorite ? 'Đã bỏ yêu thích.' : 'Đã thêm vào yêu thích.');
    }
}
