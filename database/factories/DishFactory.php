<?php

namespace Database\Factories;

use App\Models\Dish;
use App\Models\Food;
use Illuminate\Database\Eloquent\Factories\Factory;

class DishFactory extends Factory
{
    protected $model = Dish::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return ['name' => $name, 'normalized_name' => Food::normalize($name), 'description' => 'Món Việt dùng dữ liệu minh họa.', 'serving_grams' => 300, 'calories' => 400, 'protein_g' => 20, 'fat_g' => 12, 'carb_g' => 50, 'sodium_mg' => 600, 'sugar_g' => 5];
    }
}
