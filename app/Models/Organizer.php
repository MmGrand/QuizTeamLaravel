<?php

namespace App\Models;

use App\Concerns\GeneratesUniqueSlugs;
use Database\Factories\OrganizerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Game> $games
 */
#[Fillable(['name', 'slug'])]
class Organizer extends Model
{
    /** @use HasFactory<OrganizerFactory> */
    use GeneratesUniqueSlugs, HasFactory;

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Organizer $organizer) {
            if (empty($organizer->slug)) {
                $organizer->slug = static::generateUniqueSlug($organizer->name);
            }
        });

        static::updating(function (Organizer $organizer) {
            if ($organizer->isDirty('name')) {
                $organizer->slug = static::generateUniqueSlug($organizer->name, $organizer->id);
            }
        });
    }

    /**
     * Get all games run by this organizer.
     *
     * @return HasMany<Game, $this>
     */
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
