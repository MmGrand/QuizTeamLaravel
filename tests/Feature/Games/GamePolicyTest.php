<?php

namespace Tests\Feature\Games;

use App\Enums\TeamRole;
use App\Models\Game;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_any_team_member_can_record_and_edit_games()
    {
        $member = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->attach($member, ['role' => TeamRole::Member->value]);

        $game = Game::factory()->create(['team_id' => $team->id]);

        $this->assertTrue($member->can('viewAny', [Game::class, $team]));
        $this->assertTrue($member->can('create', [Game::class, $team]));
        $this->assertTrue($member->can('view', $game));
        $this->assertTrue($member->can('update', $game));
    }

    public function test_outsiders_cannot_see_or_touch_another_teams_games()
    {
        $outsider = User::factory()->create();
        $team = Team::factory()->create();
        $game = Game::factory()->create(['team_id' => $team->id]);

        $this->assertFalse($outsider->can('viewAny', [Game::class, $team]));
        $this->assertFalse($outsider->can('create', [Game::class, $team]));
        $this->assertFalse($outsider->can('view', $game));
        $this->assertFalse($outsider->can('update', $game));
        $this->assertFalse($outsider->can('delete', $game));
    }

    public function test_plain_members_cannot_delete_games()
    {
        $member = User::factory()->create();
        $team = Team::factory()->create();
        $team->members()->attach($member, ['role' => TeamRole::Member->value]);

        $game = Game::factory()->create(['team_id' => $team->id]);

        $this->assertFalse($member->can('delete', $game));
    }

    public function test_owners_and_admins_can_delete_games()
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $team = Team::factory()->create();

        $team->members()->attach($owner, ['role' => TeamRole::Owner->value]);
        $team->members()->attach($admin, ['role' => TeamRole::Admin->value]);

        $game = Game::factory()->create(['team_id' => $team->id]);

        $this->assertTrue($owner->can('delete', $game));
        $this->assertTrue($admin->can('delete', $game));
    }
}
