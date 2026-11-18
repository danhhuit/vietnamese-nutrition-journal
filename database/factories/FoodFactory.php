<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\FoodGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodFactory extends Factory
{
    protected $model = Food::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        $group = FoodGroup::firstOrCreate(['slug' => 'factory-food'], ['name_vi' => 'Nhóm kiểm thử', 'name_en' => 'Test group']);

        return ['food_group_id' => $group->id, 'name' => $name, 'normalized_name' => Food::normalize($name), 'origin' => 'plant', 'calories' => fake()->numberBetween(20, 500), 'protein_g' => 10, 'fat_g' => 5, 'carb_g' => 30];
    }
}
