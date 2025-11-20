<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Regions;
use App\Models\UserRegionRole;

class UserRegionRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run the migrations
        $this->artisan('migrate', ['--database' => 'sqlite']);
    }

    public function test_admin_regional_can_assign_admin_area_within_scope()
    {
        $assigner = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create();

        // Create two regions
        $regionA = Regions::create(['name' => 'Region A', 'type_key' => 'AREA']);
        $regionB = Regions::create(['name' => 'Region B', 'type_key' => 'AREA']);

        // Give assigner admin_regional for regionA
        UserRegionRole::create(['user_id' => $assigner->id, 'role_key' => 'admin_regional', 'region_id' => $regionA->id, 'created_by' => $assigner->id]);

        $this->actingAs($assigner)
            ->post(route('admin.users.roles.store', ['id' => $target->id]), [
                'role_key' => 'admin_area',
                'region_ids' => [$regionA->id],
            ])
            ->assertSessionHas('status', 'Assignments saved.');

        $this->assertDatabaseHas('user_region_roles', [
            'user_id' => $target->id,
            'role_key' => 'admin_area',
            'region_id' => $regionA->id,
        ]);
    }

    public function test_admin_regional_cannot_assign_admin_area_outside_scope()
    {
        $assigner = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create();

        $regionA = Regions::create(['name' => 'Region A', 'type_key' => 'AREA']);
        $regionB = Regions::create(['name' => 'Region B', 'type_key' => 'AREA']);

        // assigner only has admin_regional for regionA
        UserRegionRole::create(['user_id' => $assigner->id, 'role_key' => 'admin_regional', 'region_id' => $regionA->id, 'created_by' => $assigner->id]);

        $this->actingAs($assigner)
            ->post(route('admin.users.roles.store', ['id' => $target->id]), [
                'role_key' => 'admin_area',
                'region_ids' => [$regionB->id],
            ])
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('user_region_roles', [
            'user_id' => $target->id,
            'role_key' => 'admin_area',
            'region_id' => $regionB->id,
        ]);
    }

    public function test_webmaster_can_assign_anywhere()
    {
        $assigner = User::factory()->create(['role' => 'webmaster']);
        $target = User::factory()->create();

        $regionB = Regions::create(['name' => 'Region B', 'type_key' => 'AREA']);

        $this->actingAs($assigner)
            ->post(route('admin.users.roles.store', ['id' => $target->id]), [
                'role_key' => 'admin_area',
                'region_ids' => [$regionB->id],
            ])
            ->assertSessionHas('status', 'Assignments saved.');

        $this->assertDatabaseHas('user_region_roles', [
            'user_id' => $target->id,
            'role_key' => 'admin_area',
            'region_id' => $regionB->id,
        ]);
    }
}
