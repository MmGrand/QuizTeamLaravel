<?php

namespace Tests\Feature\Games;

use App\Enums\TeamRole;
use App\Models\Game;
use App\Models\Organizer;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Team $team;

    private Venue $venue;

    private Organizer $organizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->team = Team::factory()->create();
        $this->team->members()->attach($this->user, ['role' => TeamRole::Member->value]);
        $this->user->switchTeam($this->team);

        $this->venue = Venue::factory()->create();
        $this->organizer = Organizer::factory()->create();
    }

    /**
     * Build a valid payload for the game form.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'venue_id' => $this->venue->id,
            'organizer_id' => $this->organizer->id,
            'title' => 'Музыкальный интуитив',
            'played_at' => '2026-08-01T19:30',
            'score' => '58.5',
            'place' => 3,
            'notes' => 'Провалили географию.',
            'participants' => [$this->user->id],
        ], $overrides);
    }

    public function test_the_games_index_lists_the_teams_games()
    {
        Game::factory()->create([
            'team_id' => $this->team->id,
            'title' => 'Классика',
        ]);

        Game::factory()->create(['title' => 'Someone else game']);

        $response = $this->actingAs($this->user)->get(route('games.index', $this->team));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('games/Index')
            ->has('games', 1)
            ->where('games.0.title', 'Классика'));
    }

    public function test_the_create_form_offers_the_directories_and_the_roster()
    {
        $response = $this->actingAs($this->user)->get(route('games.create', $this->team));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('games/Create')
            ->has('venues', 1)
            ->has('organizers', 1)
            ->has('members', 1)
            ->where('members.0.id', $this->user->id));
    }

    public function test_a_member_can_record_a_game()
    {
        $response = $this->actingAs($this->user)
            ->post(route('games.store', $this->team), $this->payload());

        $response->assertRedirect(route('games.index', $this->team));

        $game = Game::sole();

        $this->assertSame($this->team->id, $game->team_id);
        $this->assertSame(58.5, $game->score);
        $this->assertSame(3, $game->place);
        $this->assertSame($this->user->id, $game->created_by);
        $this->assertTrue($game->participants->contains($this->user));
    }

    public function test_recording_a_game_requires_a_venue_and_organizer_from_the_directory()
    {
        $response = $this->actingAs($this->user)
            ->post(route('games.store', $this->team), $this->payload([
                'venue_id' => 9999,
                'organizer_id' => 9999,
            ]));

        $response->assertSessionHasErrors(['venue_id', 'organizer_id']);
        $this->assertDatabaseCount('games', 0);
    }

    public function test_scores_are_limited_to_two_decimal_places()
    {
        $response = $this->actingAs($this->user)
            ->post(route('games.store', $this->team), $this->payload(['score' => '58.555']));

        $response->assertSessionHasErrors('score');
    }

    public function test_only_team_members_can_be_recorded_as_participants()
    {
        $outsider = User::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('games.store', $this->team), $this->payload([
                'participants' => [$outsider->id],
            ]));

        $response->assertSessionHasErrors('participants.0');
        $this->assertDatabaseCount('games', 0);
    }

    public function test_a_member_can_update_a_game_and_replace_the_line_up()
    {
        $teammate = User::factory()->create();
        $this->team->members()->attach($teammate, ['role' => TeamRole::Member->value]);

        $game = Game::factory()->create(['team_id' => $this->team->id]);
        $game->participants()->attach($this->user);

        $response = $this->actingAs($this->user)
            ->patch(route('games.update', [$this->team, $game]), $this->payload([
                'title' => 'Перезаписали',
                'participants' => [$teammate->id],
            ]));

        $response->assertRedirect(route('games.index', $this->team));

        $game->refresh();

        $this->assertSame('Перезаписали', $game->title);
        $this->assertEquals([$teammate->id], $game->participants->pluck('id')->all());
    }

    public function test_a_plain_member_cannot_delete_a_game()
    {
        $game = Game::factory()->create(['team_id' => $this->team->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('games.destroy', [$this->team, $game]));

        $response->assertForbidden();
        $this->assertNotNull(Game::find($game->id));
    }

    public function test_an_admin_can_delete_a_game()
    {
        $admin = User::factory()->create();
        $this->team->members()->attach($admin, ['role' => TeamRole::Admin->value]);
        $admin->switchTeam($this->team);

        $game = Game::factory()->create(['team_id' => $this->team->id]);

        $response = $this->actingAs($admin)
            ->delete(route('games.destroy', [$this->team, $game]));

        $response->assertRedirect(route('games.index', $this->team));
        $this->assertNull(Game::find($game->id));
        $this->assertNotNull(Game::withTrashed()->find($game->id));
    }

    public function test_outsiders_cannot_reach_another_teams_games()
    {
        $outsider = User::factory()->create();
        $game = Game::factory()->create(['team_id' => $this->team->id]);

        $this->actingAs($outsider)
            ->get(route('games.index', $this->team))
            ->assertForbidden();

        $this->actingAs($outsider)
            ->get(route('games.edit', [$this->team, $game]))
            ->assertForbidden();
    }

    public function test_guests_are_redirected_to_the_login_page()
    {
        $this->get(route('games.index', $this->team))->assertRedirect(route('login'));
    }
}
