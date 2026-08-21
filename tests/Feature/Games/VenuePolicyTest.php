<?php

namespace Tests\Feature\Games;

use App\Models\Organizer;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenuePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_user_can_browse_the_directories()
    {
        $user = User::factory()->create();
        $venue = Venue::factory()->create();
        $organizer = Organizer::factory()->create();

        $this->assertTrue($user->can('viewAny', Venue::class));
        $this->assertTrue($user->can('view', $venue));
        $this->assertTrue($user->can('viewAny', Organizer::class));
        $this->assertTrue($user->can('view', $organizer));
    }

    public function test_regular_users_cannot_maintain_the_directories()
    {
        $user = User::factory()->create();
        $venue = Venue::factory()->create();
        $organizer = Organizer::factory()->create();

        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->can('create', Venue::class));
        $this->assertFalse($user->can('update', $venue));
        $this->assertFalse($user->can('delete', $venue));
        $this->assertFalse($user->can('create', Organizer::class));
        $this->assertFalse($user->can('update', $organizer));
        $this->assertFalse($user->can('delete', $organizer));
    }

    public function test_platform_admins_can_maintain_the_directories()
    {
        $admin = User::factory()->admin()->create();
        $venue = Venue::factory()->create();
        $organizer = Organizer::factory()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->can('create', Venue::class));
        $this->assertTrue($admin->can('update', $venue));
        $this->assertTrue($admin->can('delete', $venue));
        $this->assertTrue($admin->can('create', Organizer::class));
        $this->assertTrue($admin->can('update', $organizer));
        $this->assertTrue($admin->can('delete', $organizer));
    }

    public function test_the_admin_flag_cannot_be_set_through_mass_assignment()
    {
        $user = User::factory()->create();

        $user->fill(['is_admin' => true]);

        $this->assertFalse($user->is_admin);
    }
}
