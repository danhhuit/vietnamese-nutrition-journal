<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition(): array
    {
        $name = 'Quán '.fake()->unique()->company();

        return ['name' => $name, 'normalized_name' => Food::normalize($name), 'address' => fake()->address(), 'area' => 'Quận 1', 'opening_hours' => '06:00 - 22:00', 'opens_at' => '06:00', 'closes_at' => '22:00', 'phone' => '0900000000', 'price_level' => 'medium'];
    }
}
