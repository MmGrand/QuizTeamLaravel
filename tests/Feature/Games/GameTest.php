<?php

namespace Tests\Feature\Games;

use App\Models\Game;
use App\Models\Organizer;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_game_can_be_recorded_for_a_team()
    {
        $team = Team::factory()->create();
        $venue = Venue::factory()->create();
        $organizer = Organizer::factory()->create();
        $author = User::factory()->create();

        $game = Game::create([
            'team_id' => $team->id,
            'venue_id' => $venue->id,
            'organizer_id' => $organizer->id,
            'title' => 'Музыкальный интуитив',
            'played_at' => now()->subWeek(),
            'score' => 58.5,
            'place' => 3,
            'notes' => 'Провалили географию.',
            'created_by' => $author->id,
        ]);

        $this->assertDatabaseHas('games', [
            'id' => $game->id,
            'team_id' => $team->id,
            'title' => 'Музыкальный интуитив',
            'place' => 3,
        ]);
    }

    public function test_fractional_scores_survive_a_round_trip_as_floats()
    {
        $game = Game::factory()->create(['score' => 58.5]);

        $score = $game->fresh()->score;

        $this->assertIsFloat($score);
        $this->assertSame(58.5, $score);
    }

    public function test_a_game_belongs_to_its_team_venue_organizer_and_author()
    {
        $author = User::factory()->create();
        $game = Game::factory()->create(['created_by' => $author->id]);

        $this->assertInstanceOf(Team::class, $game->team);
        $this->assertInstanceOf(Venue::class, $game->venue);
        $this->assertInstanceOf(Organizer::class, $game->organizer);
        $this->assertTrue($game->createdBy->is($author));
    }

    public function test_the_author_is_detached_but_the_game_survives_when_the_user_is_deleted()
    {
        $author = User::factory()->create();
        $game = Game::factory()->create(['created_by' => $author->id]);

        $author->forceDelete();

        $this->assertNull($game->fresh()->created_by);
    }

    public function test_team_members_can_be_recorded_as_participants()
    {
        $game = Game::factory()->withParticipants(3)->create();

        $this->assertCount(3, $game->participants);
        $this->assertDatabaseCount('game_participants', 3);
    }

    public function test_a_participant_cannot_be_recorded_twice_for_the_same_game()
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();

        $game->participants()->attach($user);

        $this->expectException(QueryException::class);

        $game->participants()->attach($user);
    }

    public function test_deleting_a_game_only_soft_deletes_it()
    {
        $game = Game::factory()->create();

        $game->delete();

        $this->assertNull(Game::find($game->id));
        $this->assertNotNull(Game::withTrashed()->find($game->id));
        $this->assertDatabaseHas('games', ['id' => $game->id]);
    }

    public function test_deleting_a_team_removes_its_games()
    {
        $team = Team::factory()->create();
        $game = Game::factory()->create(['team_id' => $team->id]);

        $team->forceDelete();

        $this->assertDatabaseMissing('games', ['id' => $game->id]);
    }

    public function test_a_venue_that_has_games_cannot_be_deleted()
    {
        $venue = Venue::factory()->create();
        Game::factory()->create(['venue_id' => $venue->id]);

        $this->expectException(QueryException::class);

        $venue->delete();
    }

    public function test_an_organizer_that_has_games_cannot_be_deleted()
    {
        $organizer = Organizer::factory()->create();
        Game::factory()->create(['organizer_id' => $organizer->id]);

        $this->expectException(QueryException::class);

        $organizer->delete();
    }
}
