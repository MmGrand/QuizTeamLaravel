<?php

namespace App\Models;

use App\Concerns\GeneratesUniqueSlugs;
use Database\Factories\VenueFactory;
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
 * @property string|null $address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Game> $games
 */
#[Fillable(['name', 'slug', 'address'])]
class Venue extends Model
{
    /** @use HasFactory<VenueFactory> */
    use GeneratesUniqueSlugs, HasFactory;

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Venue $venue) {
            if (empty($venue->slug)) {
                $venue->slug = static::generateUniqueSlug($venue->name);
            }
        });

        static::updating(function (Venue $venue) {
            if ($venue->isDirty('name')) {
                $venue->slug = static::generateUniqueSlug($venue->name, $venue->id);
            }
        });
    }

    /**
     * Get all games played at this venue.
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
