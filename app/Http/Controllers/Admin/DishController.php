<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllergyTag;
use App\Models\Dish;
use App\Models\Food;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DishController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.dishes.index', ['dishes' => Dish::withCount(['ingredients', 'restaurants'])->search($request->string('q'))->orderBy('name')->paginate(20)]);
    }

    public function create(): View
    {
        return $this->form(new Dish);
    }

    public function edit(Dish $dish): View
    {
        $dish->load(['ingredients', 'allergies']);

        return $this->form($dish);
    }

    public function store(Request $request): RedirectResponse
    {
        $dish = Dish::create($this->validated($request, new Dish));
        $this->sync($request, $dish);

        return redirect()->route('admin.dishes.index')->with('status', 'Đã thêm món ăn.');
    }

    public function update(Request $request, Dish $dish): RedirectResponse
    {
        $dish->update($this->validated($request, $dish));
        $this->sync($request, $dish);

        return redirect()->route('admin.dishes.index')->with('status', 'Đã cập nhật món ăn.');
    }

    public function destroy(Dish $dish): RedirectResponse
    {
        $dish->delete();

        return back()->with('status', 'Đã xóa món ăn.');
    }

    private function form(Dish $dish): View
    {
        return view('admin.dishes.form', ['dish' => $dish, 'foods' => Food::orderBy('name')->get(), 'allergies' => AllergyTag::orderBy('name')->get()]);
    }

    private function validated(Request $request, Dish $dish): array
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:dishes,name,'.($dish->id ?: 'NULL')], 'description' => ['nullable', 'string', 'max:2000'], 'image_url' => ['nullable', 'url', 'max:500'], 'serving_grams' => ['required', 'numeric', 'between:1,3000'], 'calories' => ['required', 'numeric', 'min:0'], 'protein_g' => ['required', 'numeric', 'min:0'], 'fat_g' => ['required', 'numeric', 'min:0'], 'carb_g' => ['required', 'numeric', 'min:0'], 'fiber_g' => ['nullable', 'numeric', 'min:0'], 'sodium_mg' => ['nullable', 'numeric', 'min:0'], 'sugar_g' => ['nullable', 'numeric', 'min:0'], 'is_low_calorie' => ['nullable', 'boolean'], 'is_low_sodium' => ['nullable', 'boolean'], 'is_high_protein' => ['nullable', 'boolean'], 'is_vegetarian' => ['nullable', 'boolean'], 'data_source' => ['nullable', 'string', 'max:255'], 'ingredient_ids' => ['nullable', 'array'], 'ingredient_ids.*' => ['integer', 'exists:foods,id'], 'ingredient_grams' => ['nullable', 'array'], 'allergy_ids' => ['nullable', 'array'], 'allergy_ids.*' => ['integer', 'exists:allergy_tags,id']]);
        foreach (['is_low_calorie', 'is_low_sodium', 'is_high_protein', 'is_vegetarian'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        } $data['normalized_name'] = Food::normalize($data['name']);

        return collect($data)->except(['ingredient_ids', 'ingredient_grams', 'allergy_ids'])->all();
    }

    private function sync(Request $request, Dish $dish): void
    {
        $ingredients = [];
        foreach ($request->input('ingredient_ids', []) as $id) {
            $ingredients[$id] = ['grams' => (float) ($request->input("ingredient_grams.$id", 100))];
        } $dish->ingredients()->sync($ingredients);
        $dish->allergies()->sync($request->input('allergy_ids', []));
    }
}
