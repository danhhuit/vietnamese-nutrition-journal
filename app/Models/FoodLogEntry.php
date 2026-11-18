<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodLogEntry extends Model
{
    protected $fillable = ['food_id', 'dish_id', 'restaurant_id', 'meal_type', 'item_name', 'quantity', 'serving_grams', 'calories', 'protein_g', 'fat_g', 'carb_g', 'sodium_mg', 'sugar_g', 'source'];

    /** @return BelongsTo<DailyLog, $this> */
    public function dailyLog(): BelongsTo
    {
        return $this->belongsTo(DailyLog::class);
    }

    /** @return BelongsTo<Food, $this> */
    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    /** @return BelongsTo<Dish, $this> */
    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class);
    }

    /** @return BelongsTo<Restaurant, $this> */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
