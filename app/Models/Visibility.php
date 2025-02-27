<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Visibility
 *
 * Represents a visibility level that can be associated with various trackable entities,
 * such as goals, objectives, and trackables.
 *
 * @property int $id
 * @property string $reference
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Visibility extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The "booted" method is called when the model is being created.
     * This method generates a unique reference for the visibility.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function (Visibility $visibility) {
            do {
                // Generate a unique reference for the visibility
                $ref = 'VIS' . substr(
                        str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),
                        0, 8);

            } while (Visibility::where('reference', $ref)->exists());

            $visibility->reference = $ref;
        });
    }

    /**
     * Get the goals associated with the visibility.
     *
     * @return HasMany
     */
    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    /**
     * Get the objectives associated with the visibility.
     *
     * @return HasMany
     */
    public function objectives(): HasMany
    {
        return $this->hasMany(Objective::class);
    }

    /**
     * Get the trackables associated with the visibility.
     *
     * @return HasMany
     */
    public function trackables(): HasMany
    {
        return $this->hasMany(Trackable::class);
    }
}
