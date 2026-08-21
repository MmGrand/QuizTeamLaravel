<?php

namespace App\Http\Controllers;

use App\Enums\TeamPermission;
use App\Http\Requests\Games\SaveGameRequest;
use App\Models\Game;
use App\Models\Organizer;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    /**
     * Display the games recorded by the current team.
     */
    public function index(Request $request, Team $current_team): Response
    {
        Gate::authorize('viewAny', [Game::class, $current_team]);

        $games = $current_team->games()
            ->with(['venue', 'organizer', 'participants'])
            ->orderByDesc('played_at')
            ->get()
            ->map(fn (Game $game) => $this->toListItem($game));

        return Inertia::render('games/Index', [
            'games' => $games,
            'permissions' => [
                'canDeleteGame' => $request->user()->hasTeamPermission($current_team, TeamPermission::DeleteGame),
            ],
        ]);
    }

    /**
     * Show the form for recording a new game.
     */
    public function create(Team $current_team): Response
    {
        Gate::authorize('create', [Game::class, $current_team]);

        return Inertia::render('games/Create', $this->formOptions($current_team));
    }

    /**
     * Store a newly recorded game.
     */
    public function store(SaveGameRequest $request, Team $current_team): RedirectResponse
    {
        Gate::authorize('create', [Game::class, $current_team]);

        DB::transaction(function () use ($request, $current_team) {
            $game = $current_team->games()->create([
                ...$request->safe()->except('participants'),
                'created_by' => $request->user()->id,
            ]);

            $game->participants()->sync($request->validated('participants', []));
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Game recorded.')]);

        return to_route('games.index');
    }

    /**
     * Show the form for editing the given game.
     */
    public function edit(Team $current_team, Game $game): Response
    {
        Gate::authorize('update', $game);

        $game->load(['participants']);

        return Inertia::render('games/Edit', [
            ...$this->formOptions($current_team),
            'game' => [
                'id' => $game->id,
                'venueId' => $game->venue_id,
                'organizerId' => $game->organizer_id,
                'title' => $game->title,
                'playedAt' => $game->played_at->format('Y-m-d\TH:i'),
                'score' => $game->score,
                'place' => $game->place,
                'notes' => $game->notes,
                'participantIds' => $game->participants->pluck('id'),
            ],
        ]);
    }

    /**
     * Update the given game.
     */
    public function update(SaveGameRequest $request, Team $current_team, Game $game): RedirectResponse
    {
        Gate::authorize('update', $game);

        DB::transaction(function () use ($request, $game) {
            $game->update($request->safe()->except('participants'));

            $game->participants()->sync($request->validated('participants', []));
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Game updated.')]);

        return to_route('games.index');
    }

    /**
     * Remove the given game.
     */
    public function destroy(Team $current_team, Game $game): RedirectResponse
    {
        Gate::authorize('delete', $game);

        $game->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Game deleted.')]);

        return to_route('games.index');
    }

    /**
     * Get the directory and roster options shared by the create and edit forms.
     *
     * @return array<string, mixed>
     */
    private function formOptions(Team $team): array
    {
        return [
            'venues' => Venue::orderBy('name')
                ->get()
                ->map(fn (Venue $venue) => [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'address' => $venue->address,
                ]),
            'organizers' => Organizer::orderBy('name')
                ->get()
                ->map(fn (Organizer $organizer) => [
                    'id' => $organizer->id,
                    'name' => $organizer->name,
                ]),
            'members' => $team->members()
                ->orderBy('name')
                ->get()
                ->map(fn (User $member) => [
                    'id' => $member->id,
                    'name' => $member->name,
                ]),
        ];
    }

    /**
     * Shape a game for the index listing.
     *
     * @return array<string, mixed>
     */
    private function toListItem(Game $game): array
    {
        return [
            'id' => $game->id,
            'title' => $game->title,
            'playedAt' => $game->played_at->toISOString(),
            'score' => $game->score,
            'place' => $game->place,
            'notes' => $game->notes,
            'venue' => ['name' => $game->venue->name],
            'organizer' => ['name' => $game->organizer->name],
            'participants' => $game->participants
                ->map(fn (User $member) => ['id' => $member->id, 'name' => $member->name]),
        ];
    }
}
