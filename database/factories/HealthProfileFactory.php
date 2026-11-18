<?php

namespace Database\Factories;

use App\Models\HealthProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class HealthProfileFactory extends Factory
{
    protected $model = HealthProfile::class;

    public function definition(): array
    {
        return ['user_id' => User::factory(), 'birth_date' => '1995-01-01', 'gender' => 'male', 'height_cm' => 170, 'weight_kg' => 65, 'activity_level' => 'moderate', 'goal' => 'maintain', 'daily_calorie_target' => 2200, 'water_target_ml' => 2000];
    }
}
