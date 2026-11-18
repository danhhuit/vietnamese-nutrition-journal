<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class FoodController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.foods.index', ['foods' => Food::with('foodGroup')->search($request->string('q'))->orderBy('name')->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.foods.form', ['food' => new Food, 'groups' => FoodGroup::orderBy('name_vi')->get()]);
    }

    public function edit(Food $food): View
    {
        return view('admin.foods.form', ['food' => $food, 'groups' => FoodGroup::orderBy('name_vi')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Food::create($this->validated($request, new Food));

        return redirect()->route('admin.foods.index')->with('status', 'Đã thêm thực phẩm.');
    }

    public function update(Request $request, Food $food): RedirectResponse
    {
        $food->update($this->validated($request, $food));

        return redirect()->route('admin.foods.index')->with('status', 'Đã cập nhật thực phẩm.');
    }

    public function destroy(Food $food): RedirectResponse
    {
        abort_if($food->dishes()->exists(), 422, 'Thực phẩm đang được dùng trong món ăn.');
        $food->delete();

        return back()->with('status', 'Đã xóa thực phẩm.');
    }

    private function validated(Request $request, Food $food): array
    {
        $data = $request->validate(['food_group_id' => ['required', 'exists:food_groups,id'], 'name' => ['required', 'string', 'max:255', 'unique:foods,name,'.($food->id ?: 'NULL')], 'origin' => ['required', 'in:animal,plant,mixed'], 'source' => ['nullable', 'string', 'max:255'], 'calories' => ['required', 'numeric', 'min:0'], 'protein_g' => ['required', 'numeric', 'min:0'], 'fat_g' => ['required', 'numeric', 'min:0'], 'carb_g' => ['required', 'numeric', 'min:0'], 'fiber_g' => ['nullable', 'numeric', 'min:0'], 'calcium_mg' => ['nullable', 'numeric', 'min:0'], 'iron_mg' => ['nullable', 'numeric', 'min:0'], 'sodium_mg' => ['nullable', 'numeric', 'min:0'], 'potassium_mg' => ['nullable', 'numeric', 'min:0'], 'vitamin_a_mcg' => ['nullable', 'numeric', 'min:0'], 'vitamin_c_mg' => ['nullable', 'numeric', 'min:0']]);
        $data['normalized_name'] = Food::normalize($data['name']);

        return $data;
    }
}
