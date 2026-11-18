<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class JournalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_update_and_remove_a_dish_from_their_journal(): void
    {
        $user = User::factory()->create();
        $dish = Dish::factory()->create(['calories' => 400, 'serving_grams' => 300]);

        $this->actingAs($user)->post('/journal/entries', [
            'dish_id' => $dish->id,
            'meal_type' => 'lunch',
            'quantity' => 1.5,
            'logged_on' => now()->toDateString(),
        ])->assertRedirect();

        $entryId = $user->dailyLogs()->firstOrFail()->entries()->firstOrFail()->id;
        $this->assertDatabaseHas('food_log_entries', ['id' => $entryId, 'calories' => 600]);

        $this->actingAs($user)->patch("/journal/entries/{$entryId}", ['quantity' => 1])->assertRedirect();
        $this->assertDatabaseHas('food_log_entries', ['id' => $entryId, 'calories' => 400]);

        $this->actingAs($user)->delete("/journal/entries/{$entryId}")->assertRedirect();
        $this->assertDatabaseMissing('food_log_entries', ['id' => $entryId]);
    }

    public function test_users_cannot_delete_another_users_entry(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $dish = Dish::factory()->create();
        $log = $owner->dailyLogs()->create(['logged_on' => now()->toDateString()]);
        $entry = $log->entries()->create([
            'dish_id' => $dish->id,
            'meal_type' => 'dinner',
            'quantity' => 1,
            'serving_grams' => 300,
            'calories' => 400,
            'protein_g' => 20,
            'fat_g' => 10,
            'carb_g' => 55,
        ]);

        $this->actingAs($attacker)->delete("/journal/entries/{$entry->id}")->assertForbidden();
    }
}
