<?php

namespace App\Http\Controllers;

use App\Models\AllergyTag;
use App\Models\Setting;
use App\Services\NutritionCalculator;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class HealthProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.health', ['profile' => $request->user()->healthProfile, 'allergies' => AllergyTag::orderBy('name')->get()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(['birth_date' => ['required', 'date', 'before:today'], 'gender' => ['required', 'in:male,female'], 'height_cm' => ['required', 'numeric', 'between:100,250'], 'weight_kg' => ['required', 'numeric', 'between:30,300'], 'activity_level' => ['required', 'in:sedentary,light,moderate,active,very_active'], 'goal' => ['required', 'in:maintain,lose,gain'], 'target_weight_kg' => ['nullable', 'numeric', 'between:30,300'], 'target_date' => ['nullable', 'date', 'after:today'], 'water_target_ml' => ['required', 'integer', 'between:500,6000'], 'allergy_ids' => ['nullable', 'array'], 'allergy_ids.*' => ['integer', 'exists:allergy_tags,id'], 'reminders' => ['nullable', 'array']]);
        $age = Carbon::parse($data['birth_date'])->age;
        $metrics = (new NutritionCalculator(Setting::numbers()))->calculate($data['gender'], (float) $data['weight_kg'], (float) $data['height_cm'], $age, $data['activity_level'], $data['goal']);
        $profile = $request->user()->healthProfile()->updateOrCreate([], array_merge(collect($data)->except('allergy_ids')->all(), ['daily_calorie_target' => $metrics['daily_calories'], 'protein_target_g' => $metrics['protein_g'], 'fat_target_g' => $metrics['fat_g'], 'carb_target_g' => $metrics['carb_g']]));
        $profile->allergies()->sync($data['allergy_ids'] ?? []);

        return back()->with('status', 'Đã cập nhật hồ sơ và hạn mức dinh dưỡng.');
    }

    public function preferences(Request $request): RedirectResponse
    {
        $data = $request->validate(['locale' => ['required', 'in:vi,en'], 'dark_mode' => ['nullable', 'boolean']]);
        $request->user()->update(['locale' => $data['locale'], 'dark_mode' => $request->boolean('dark_mode')]);

        return back()->with('status', 'Đã cập nhật ngôn ngữ và giao diện.');
    }
}
