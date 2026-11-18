<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dish;
use App\Models\Food;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class RestaurantController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.restaurants.index', ['restaurants' => Restaurant::withCount('dishes')->search($request->string('q'))->orderBy('name')->paginate(20)]);
    }

    public function create(): View
    {
        return $this->form(new Restaurant);
    }

    public function edit(Restaurant $restaurant): View
    {
        $restaurant->load('dishes');

        return $this->form($restaurant);
    }

    public function store(Request $request): RedirectResponse
    {
        $restaurant = Restaurant::create($this->validated($request));
        $this->syncMenu($request, $restaurant);

        return redirect()->route('admin.restaurants.index')->with('status', 'Đã thêm quán ăn.');
    }

    public function update(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $restaurant->update($this->validated($request));
        $this->syncMenu($request, $restaurant);

        return redirect()->route('admin.restaurants.index')->with('status', 'Đã cập nhật quán ăn.');
    }

    public function destroy(Restaurant $restaurant): RedirectResponse
    {
        $restaurant->delete();

        return back()->with('status', 'Đã xóa quán ăn.');
    }

    private function form(Restaurant $restaurant): View
    {
        return view('admin.restaurants.form', ['restaurant' => $restaurant, 'dishes' => Dish::orderBy('name')->get()]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'address' => ['required', 'string', 'max:500'], 'area' => ['required', 'string', 'max:100'], 'latitude' => ['nullable', 'numeric', 'between:-90,90'], 'longitude' => ['nullable', 'numeric', 'between:-180,180'], 'opening_hours' => ['nullable', 'string', 'max:100'], 'opens_at' => ['nullable', 'date_format:H:i'], 'closes_at' => ['nullable', 'date_format:H:i'], 'phone' => ['nullable', 'string', 'max:30'], 'price_level' => ['required', 'in:budget,medium,premium'], 'image_url' => ['nullable', 'url', 'max:500'], 'dish_ids' => ['nullable', 'array'], 'dish_ids.*' => ['integer', 'exists:dishes,id'], 'prices' => ['nullable', 'array']]);
        $data['normalized_name'] = Food::normalize($data['name']);
        $data['data_updated_at'] = today();

        return collect($data)->except(['dish_ids', 'prices'])->all();
    }

    private function syncMenu(Request $request, Restaurant $restaurant): void
    {
        $menu = [];
        foreach ($request->input('dish_ids', []) as $id) {
            $menu[$id] = ['price' => (int) ($request->input("prices.$id", 30000)), 'is_available' => true];
        } $restaurant->dishes()->sync($menu);
    }
}
