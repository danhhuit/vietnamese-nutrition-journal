<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    protected $fillable = ['food_group_id', 'name', 'normalized_name', 'origin', 'source', 'calories', 'protein_g', 'fat_g', 'carb_g', 'fiber_g', 'calcium_mg', 'iron_mg', 'sodium_mg', 'potassium_mg', 'vitamin_a_mcg', 'vitamin_c_mg', 'viewed_count'];

    public function foodGroup(): BelongsTo
    {
        return $this->belongsTo(FoodGroup::class);
    }

    public function dishes(): BelongsToMany
    {
        return $this->belongsToMany(Dish::class)->withPivot('grams');
    }

    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $term ? $query->where('normalized_name', 'like', '%'.self::normalize($term).'%') : $query;
    }

    public static function normalize(string $value): string
    {
        return strtolower(iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value);
    }
}
