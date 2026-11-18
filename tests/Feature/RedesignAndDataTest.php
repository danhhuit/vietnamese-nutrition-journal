<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Food;
use App\Models\PermissionGroup;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RedesignAndDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_food_has_traceable_2017_values_and_restaurants_have_real_address_sources(): void
    {
        $this->seed();

        $sweetPotato = Food::where('name', 'Khoai lang')->firstOrFail();
        self::assertGreaterThanOrEqual(50, Food::count());
        self::assertSame(124.0, (float) $sweetPotato->calories);
        self::assertStringContainsString('02008', $sweetPotato->source);
        self::assertSame(112.0, (float) Food::where('name', 'Bún tươi')->firstOrFail()->calories);
        self::assertSame(46, Food::query()->where('source', 'like', '%2017%')->count());
        self::assertSame(16, Restaurant::count());
        self::assertSame('84 Đặng Văn Ngữ, Phú Nhuận, TP. Hồ Chí Minh', Restaurant::where('name', 'Cơm Tấm Ba Ghiền')->firstOrFail()->address);
        self::assertTrue(Restaurant::query()->whereNull('source_url')->doesntExist());
        self::assertTrue(Restaurant::query()->whereNotNull('latitude')->doesntExist());
    }

    public function test_permission_group_limits_admin_routes_and_requests_are_audited(): void
    {
        $group = PermissionGroup::create(['name' => 'Kiểm toán', 'permissions' => ['view_activity']]);
        $user = User::factory()->create();
        $user->forceFill(['permission_group_id' => $group->id])->save();

        $this->actingAs($user)->get(route('admin.activity.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.settings.index'))->assertForbidden();
        self::assertTrue(ActivityLog::where('user_id', $user->id)->where('route_name', 'admin.activity.index')->exists());
    }

    public function test_guest_can_switch_language(): void
    {
        $this->post(route('locale.update'), ['locale' => 'en'])->assertRedirect();
        $this->get(route('home'))->assertSee('Vietnamese Nutrition');
    }
}
