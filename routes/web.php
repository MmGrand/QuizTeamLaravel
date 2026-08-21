<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        Route::get('games', [GameController::class, 'index'])->name('games.index');
        Route::get('games/create', [GameController::class, 'create'])->name('games.create');
        Route::post('games', [GameController::class, 'store'])->name('games.store');
        Route::get('games/{game}/edit', [GameController::class, 'edit'])->name('games.edit');
        Route::patch('games/{game}', [GameController::class, 'update'])->name('games.update');
        Route::delete('games/{game}', [GameController::class, 'destroy'])->name('games.destroy');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

require __DIR__.'/settings.php';
