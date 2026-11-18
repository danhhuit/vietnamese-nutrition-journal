<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Food;
use App\Models\FoodLogEntry;
use App\Services\NutritionSummary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class JournalController extends Controller
{
    public function index(Request $request, NutritionSummary $summary): View
    {
        $date = $request->date('date')?->toDateString() ?? today()->toDateString();
        $log = $request->user()->dailyLogs()->with(['entries.dish', 'entries.food'])->whereDate('logged_on', $date)->first()
            ?? $request->user()->dailyLogs()->create(['logged_on' => $date]);
        $totals = $summary->totals($log);
        $target = (float) ($request->user()->healthProfile?->daily_calorie_target ?: 2000);

        return view('journal.index', ['date' => $date, 'log' => $log, 'totals' => $totals, 'target' => $target, 'warnings' => $summary->warnings($totals, $target), 'dishes' => Dish::orderBy('name')->get(), 'foods' => Food::orderBy('name')->get(), 'waterTotal' => $request->user()->waterLogs()->whereDate('logged_on', $date)->sum('amount_ml')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['dish_id' => ['nullable', 'required_without:food_id', 'exists:dishes,id'], 'food_id' => ['nullable', 'required_without:dish_id', 'exists:foods,id'], 'restaurant_id' => ['nullable', 'exists:restaurants,id'], 'meal_type' => ['required', 'in:breakfast,lunch,dinner,snack'], 'quantity' => ['required', 'numeric', 'between:0.1,20'], 'logged_on' => ['required', 'date'], 'serving_grams' => ['nullable', 'numeric', 'between:1,3000']]);
        $log = $request->user()->dailyLogs()->whereDate('logged_on', $data['logged_on'])->first()
            ?? $request->user()->dailyLogs()->create(['logged_on' => $data['logged_on']]);
        $item = isset($data['dish_id']) ? Dish::findOrFail($data['dish_id']) : Food::findOrFail($data['food_id']);
        $baseGrams = $item instanceof Dish ? (float) $item->serving_grams : 100.0;
        $grams = (float) ($data['serving_grams'] ?? $baseGrams);
        $factor = ($grams / $baseGrams) * (float) $data['quantity'];
        $log->entries()->create($this->entryData($item, $data, $grams, $factor));

        return back()->with('status', 'Đã thêm vào nhật ký.');
    }

    public function update(Request $request, FoodLogEntry $entry): RedirectResponse
    {
        $this->authorizeEntry($request, $entry);
        $data = $request->validate(['quantity' => ['required', 'numeric', 'between:0.1,20']]);
        $old = max((float) $entry->quantity, .01);
        $factor = (float) $data['quantity'] / $old;
        $entry->update(['quantity' => $data['quantity'], 'calories' => round((float) $entry->calories * $factor, 1), 'protein_g' => round((float) $entry->protein_g * $factor, 1), 'fat_g' => round((float) $entry->fat_g * $factor, 1), 'carb_g' => round((float) $entry->carb_g * $factor, 1), 'sodium_mg' => round((float) $entry->sodium_mg * $factor, 1), 'sugar_g' => round((float) $entry->sugar_g * $factor, 1)]);

        return back()->with('status', 'Đã cập nhật khẩu phần.');
    }

    public function destroy(Request $request, FoodLogEntry $entry): RedirectResponse
    {
        $this->authorizeEntry($request, $entry);
        $entry->delete();

        return back()->with('status', 'Đã xóa món khỏi nhật ký.');
    }

    public function water(Request $request): RedirectResponse
    {
        $data = $request->validate(['logged_on' => ['required', 'date'], 'amount_ml' => ['required', 'integer', 'between:50,3000']]);
        $request->user()->waterLogs()->create($data);

        return back()->with('status', 'Đã ghi lượng nước.');
    }

    public function weight(Request $request): RedirectResponse
    {
        $data = $request->validate(['recorded_on' => ['required', 'date'], 'weight_kg' => ['required', 'numeric', 'between:30,300']]);
        $request->user()->weightEntries()->updateOrCreate(['recorded_on' => $data['recorded_on']], ['weight_kg' => $data['weight_kg']]);
        $request->user()->healthProfile?->update(['weight_kg' => $data['weight_kg']]);

        return back()->with('status', 'Đã ghi cân nặng.');
    }

    private function entryData(Dish|Food $item, array $data, float $grams, float $factor): array
    {
        return ['dish_id' => $item instanceof Dish ? $item->id : null, 'food_id' => $item instanceof Food ? $item->id : null, 'restaurant_id' => $data['restaurant_id'] ?? null, 'meal_type' => $data['meal_type'], 'item_name' => $item->name, 'quantity' => $data['quantity'], 'serving_grams' => $grams, 'calories' => round((float) $item->calories * $factor, 1), 'protein_g' => round((float) $item->protein_g * $factor, 1), 'fat_g' => round((float) $item->fat_g * $factor, 1), 'carb_g' => round((float) $item->carb_g * $factor, 1), 'sodium_mg' => round((float) $item->sodium_mg * $factor, 1), 'sugar_g' => round((float) ($item->sugar_g ?? 0) * $factor, 1), 'source' => isset($data['restaurant_id']) ? 'restaurant' : 'database'];
    }

    private function authorizeEntry(Request $request, FoodLogEntry $entry): void
    {
        abort_unless($entry->dailyLog()->where('user_id', $request->user()->id)->exists(), 403);
    }
}
