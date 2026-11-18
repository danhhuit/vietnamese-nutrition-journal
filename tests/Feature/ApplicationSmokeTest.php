<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ApplicationSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_user_can_render_every_primary_application_module(): void
    {
        $this->seed();
        $user = User::where('email', 'user@dinhduong.test')->firstOrFail();
        $dish = Dish::firstOrFail();
        $restaurant = Restaurant::firstOrFail();

        foreach ([
            route('dashboard'), route('foods.index'), route('dishes.index'), route('dishes.show', $dish),
            route('restaurants.index'), route('restaurants.show', $restaurant), route('compare'),
            route('journal.index'), route('suggestions'), route('reports.index'), route('profile.edit'),
            route('health-profile.edit'), route('favorites.index'), route('about'),
        ] as $url) {
            $this->actingAs($user)->get($url)->assertOk();
        }

        $this->actingAs($user)->get(route('reports.pdf'))->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    public function test_seeded_admin_can_render_all_administration_modules(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@dinhduong.test')->firstOrFail();

        foreach ([
            route('admin.dashboard'), route('admin.foods.index'), route('admin.foods.create'),
            route('admin.dishes.index'), route('admin.dishes.create'), route('admin.restaurants.index'),
            route('admin.restaurants.create'), route('admin.taxonomies.index'), route('admin.users.index'),
            route('admin.settings.index'),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_english_preference_translates_primary_navigation(): void
    {
        $user = User::factory()->create(['locale' => 'en']);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Vietnamese dishes')
            ->assertSee('Today&#039;s nutrition overview', false);
    }
}
