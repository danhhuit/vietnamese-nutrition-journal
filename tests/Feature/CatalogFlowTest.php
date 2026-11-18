<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\Food;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CatalogFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_smart_search_finds_vietnamese_names_without_accents(): void
    {
        Food::factory()->create(['name' => 'Gạo tẻ', 'normalized_name' => 'gao te']);
        Dish::factory()->create(['name' => 'Phở bò', 'normalized_name' => 'pho bo']);
        Restaurant::factory()->create(['name' => 'Quán Việt', 'normalized_name' => 'quan viet']);

        $this->get('/search?q=pho+bo')
            ->assertOk()
            ->assertSee('Phở bò');
    }

    public function test_dish_page_lists_restaurants_and_scaled_nutrition(): void
    {
        $dish = Dish::factory()->create(['name' => 'Phở bò', 'calories' => 450, 'serving_grams' => 400]);
        $restaurant = Restaurant::factory()->create(['name' => 'Phở Nhà']);
        $restaurant->dishes()->attach($dish, ['price' => 55000]);

        $this->get("/dishes/{$dish->id}?grams=200")
            ->assertOk()
            ->assertSee('225')
            ->assertSee('Phở Nhà');
    }
}
