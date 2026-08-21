<?php

namespace Database\Factories;

use App\Enums\TeamRole;
use App\Models\Game;
use App\Models\Organizer;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'venue_id' => Venue::factory(),
            'organizer_id' => Organizer::factory(),
            'title' => fake()->sentence(3),
            'played_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'score' => fake()->randomFloat(1, 20, 90),
            'place' => fake()->numberBetween(1, 20),
            'notes' => null,
            'created_by' => null,
        ];
    }

    /**
     * Indicate that the team won the game.
     */
    public function won(): static
    {
        return $this->state(fn (array $attributes) => [
            'place' => 1,
        ]);
    }

    /**
     * Indicate that the final standings are unknown.
     */
    public function withoutPlace(): static
    {
        return $this->state(fn (array $attributes) => [
            'place' => null,
        ]);
    }

    /**
     * Indicate that the game has been deleted.
     */
    public function trashed(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
        ]);
    }

    /**
     * Add the given number of team members as participants of the game.
     */
    public function withParticipants(int $count = 3): static
    {
        return $this->afterCreating(function (Game $game) use ($count) {
            User::factory()
                ->count($count)
                ->create()
                ->each(function (User $user) use ($game) {
                    $game->team->memberships()->create([
                        'user_id' => $user->id,
                        'role' => TeamRole::Member,
                    ]);

                    $game->participants()->attach($user);
                });
        });
    }
}
