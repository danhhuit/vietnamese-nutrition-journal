<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HealthProfile extends Model
{
    use HasFactory;

    protected $fillable = ['birth_date', 'gender', 'height_cm', 'weight_kg', 'activity_level', 'goal', 'target_weight_kg', 'target_date', 'daily_calorie_target', 'protein_target_g', 'fat_target_g', 'carb_target_g', 'water_target_ml', 'dietary_preferences', 'reminders'];

    protected function casts(): array
    {
        return ['birth_date' => 'date', 'target_date' => 'date', 'dietary_preferences' => 'array', 'reminders' => 'array'];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsToMany<AllergyTag, $this> */
    public function allergies(): BelongsToMany
    {
        return $this->belongsToMany(AllergyTag::class);
    }
}
