<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\Restaurant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class InterfaceAndOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalogs_show_result_counts_and_support_sorting(): void
    {
        Dish::factory()->create(['name' => 'Món ít năng lượng', 'normalized_name' => 'mon it nang luong', 'calories' => 100]);
        Dish::factory()->create(['name' => 'Món nhiều năng lượng', 'normalized_name' => 'mon nhieu nang luong', 'calories' => 700]);
        Restaurant::factory()->create(['name' => 'Quán A', 'normalized_name' => 'quan a']);

        $this->get(route('dishes.index', ['sort' => 'calories_desc']))
            ->assertOk()->assertSee('2 kết quả')
            ->assertSeeInOrder(['Món nhiều năng lượng', 'Món ít năng lượng']);
        $this->get(route('restaurants.index', ['sort' => 'name_asc']))
            ->assertOk()->assertSee('1 kết quả');
    }

    public function test_guest_language_selection_translates_catalog_heading(): void
    {
        $this->post(route('locale.update'), ['locale' => 'en'])->assertRedirect();
        $this->get(route('foods.index'))->assertOk()->assertSee('Food lookup');
    }

    public function test_admin_navigation_is_rendered_and_not_leaked_as_blade_source(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee("@include('admin._nav')")
            ->assertSee('data-admin-navigation', false);
    }

    public function test_maintenance_mode_blocks_users_but_keeps_admin_access(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->admin()->create();
        Setting::create(['key' => 'maintenance_enabled', 'label' => 'Bảo trì', 'value' => '1', 'type' => 'boolean']);
        Setting::create(['key' => 'maintenance_message', 'label' => 'Thông báo', 'value' => 'Hệ thống đang nâng cấp.', 'type' => 'text']);

        $this->actingAs($user)->get(route('dashboard'))->assertStatus(503)->assertSee('Hệ thống đang nâng cấp.');
        $this->actingAs($admin)->get(route('admin.system.index'))->assertOk();
    }

    public function test_admin_can_create_and_restore_a_json_backup(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();
        Setting::create(['key' => 'sample_setting', 'label' => 'Mẫu', 'value' => 'before', 'type' => 'text']);

        $this->actingAs($admin)->post(route('admin.system.backup'))->assertRedirect();
        $backupPath = collect(Storage::disk('local')->files('backups'))->firstOrFail();
        Setting::where('key', 'sample_setting')->update(['value' => 'after']);

        $upload = UploadedFile::fake()->createWithContent('backup.json', Storage::disk('local')->get($backupPath));
        $this->actingAs($admin)->post(route('admin.system.restore'), ['backup' => $upload, 'confirmation' => 'RESTORE'])->assertRedirect();

        self::assertSame('before', Setting::where('key', 'sample_setting')->value('value'));
    }
}
