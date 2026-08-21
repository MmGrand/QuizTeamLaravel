<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Number of recent games shown on the dashboard.
     */
    private const RECENT_GAMES = 5;

    public function __invoke(Request $request, ?Team $current_team = null): Response
    {
        return Inertia::render('Dashboard', [
            'pendingInvitations' => $this->pendingInvitations($request->user()),
            'stats' => $current_team ? $this->stats($current_team) : null,
            'recentGames' => $current_team ? $this->recentGames($current_team) : [],
        ]);
    }

    /**
     * Get the invitations waiting for the given user.
     *
     * @return array<int, array{code: string, inviterName: string, team: array{name: string, slug: string}}>
     */
    private function pendingInvitations(User $user): array
    {
        return TeamInvitation::query()
            ->with(['inviter', 'team'])
            ->whereRaw('LOWER(email) = ?', [strtolower($user->email)])
            ->whereNull('accepted_at')
            ->where(fn ($query) => $query
                ->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now()))
            ->latest()
            ->get()
            ->map(fn (TeamInvitation $invitation) => [
                'code' => $invitation->code,
                'inviterName' => $invitation->inviter->name,
                'team' => [
                    'name' => $invitation->team->name,
                    'slug' => $invitation->team->slug,
                ],
            ])
            ->values()
            ->all();
    }

    /**
     * Summarise the team's whole history.
     *
     * @return array{gamesCount: int, totalScore: float, averageScore: float|null, bestScore: float|null, wins: int}
     */
    private function stats(Team $team): array
    {
        $averageScore = $team->games()->avg('score');
        $bestScore = $team->games()->max('score');

        return [
            'gamesCount' => $team->games()->count(),
            'totalScore' => round((float) $team->games()->sum('score'), 2),
            'averageScore' => $averageScore === null ? null : round((float) $averageScore, 1),
            'bestScore' => $bestScore === null ? null : round((float) $bestScore, 2),
            'wins' => $team->games()->where('place', 1)->count(),
        ];
    }

    /**
     * Get the team's most recent games.
     *
     * @return array<int, array{id: int, title: string|null, playedAt: string|null, score: float, place: int|null, venue: array{name: string}, organizer: array{name: string}}>
     */
    private function recentGames(Team $team): array
    {
        return $team->games()
            ->with(['venue', 'organizer'])
            ->orderByDesc('played_at')
            ->limit(self::RECENT_GAMES)
            ->get()
            ->map(fn (Game $game) => [
                'id' => $game->id,
                'title' => $game->title,
                'playedAt' => $game->played_at->toISOString(),
                'score' => $game->score,
                'place' => $game->place,
                'venue' => ['name' => $game->venue->name],
                'organizer' => ['name' => $game->organizer->name],
            ])
            ->values()
            ->all();
    }
}
