<?php

namespace Tests\Unit;

use App\Models\AllergyTag;
use App\Models\Dish;
use App\Models\HealthProfile;
use App\Models\Restaurant;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_respects_remaining_calories_goal_and_allergies(): void
    {
        $user = User::factory()->create();
        $profile = HealthProfile::factory()->for($user)->create([
            'goal' => 'lose',
            'daily_calorie_target' => 1800,
        ]);
        $allergy = AllergyTag::create(['name' => 'Đậu phộng', 'slug' => 'dau-phong']);
        $profile->allergies()->attach($allergy);

        $safe = Dish::factory()->create(['name' => 'Gỏi cuốn', 'calories' => 280, 'is_low_calorie' => true]);
        $unsafe = Dish::factory()->create(['name' => 'Bún thịt nướng', 'calories' => 450, 'is_low_calorie' => true]);
        $unsafe->allergies()->attach($allergy);
        Dish::factory()->create(['name' => 'Cơm tấm', 'calories' => 700, 'is_low_calorie' => false]);
        $restaurant = Restaurant::factory()->create();
        $restaurant->dishes()->attach($safe, ['price' => 35000]);

        $results = app(RecommendationService::class)->forUser($user, remainingCalories: 500);

        self::assertCount(1, $results);
        self::assertTrue($results->first()->is($safe));
        self::assertTrue($results->first()->relationLoaded('restaurants'));
    }
}
