<?php

namespace App\Services;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class RecommendationService
{
    /** @return Collection<int, Dish> */
    public function forUser(User $user, float $remainingCalories, ?string $goal = null, int $limit = 12): Collection
    {
        $profile = $user->healthProfile()->with('allergies')->first();
        if ($profile === null) {
            $goal ??= 'maintain';
            $allergyIds = [];
        } else {
            $goal ??= $profile->goal;
            $allergyIds = $profile->allergies->modelKeys();
        }

        return Dish::query()
            ->with(['restaurants' => fn ($query) => $query->wherePivot('is_available', true)])
            ->where('calories', '<=', max(0, $remainingCalories))
            ->when($goal === 'lose', fn ($query) => $query->where('is_low_calorie', true))
            ->when($goal === 'gain', fn ($query) => $query->where('is_high_protein', true))
            ->when($goal === 'low_sodium', fn ($query) => $query->where('is_low_sodium', true))
            ->when($goal === 'vegetarian', fn ($query) => $query->where('is_vegetarian', true))
            ->when($allergyIds, fn ($query) => $query->whereDoesntHave('allergies', fn ($allergies) => $allergies->whereIn('allergy_tags.id', $allergyIds)))
            ->orderByDesc('protein_g')
            ->orderBy('calories')
            ->limit($limit)
            ->get();
    }
}
