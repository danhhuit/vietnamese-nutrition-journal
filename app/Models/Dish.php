<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'normalized_name', 'description', 'image_url', 'serving_grams', 'calories', 'protein_g', 'fat_g', 'carb_g', 'fiber_g', 'sodium_mg', 'sugar_g', 'is_low_calorie', 'is_low_sodium', 'is_high_protein', 'is_vegetarian', 'data_source', 'viewed_count'];

    protected function casts(): array
    {
        return ['is_low_calorie' => 'boolean', 'is_low_sodium' => 'boolean', 'is_high_protein' => 'boolean', 'is_vegetarian' => 'boolean'];
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Food::class)->withPivot('grams');
    }

    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class)->withPivot(['price', 'is_available'])->withTimestamps();
    }

    public function allergies(): BelongsToMany
    {
        return $this->belongsToMany(AllergyTag::class);
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $term ? $query->where('normalized_name', 'like', '%'.Food::normalize($term).'%') : $query;
    }

    public function scaled(float $grams): array
    {
        $factor = $grams / max((float) $this->serving_grams, 1);

        return collect(['calories', 'protein_g', 'fat_g', 'carb_g', 'fiber_g', 'sodium_mg', 'sugar_g'])->mapWithKeys(fn (string $key): array => [$key => round((float) $this->{$key} * $factor, 1)])->all();
    }
}
