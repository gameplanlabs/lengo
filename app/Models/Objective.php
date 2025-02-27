<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class Objective
 *
 * Represents an objective that can be tracked and associated with goals.
 *
 * @property int $id
 * @property string $reference
 * @property string $title
 * @property string $description
 * @property string $status
 * @property string $priority
 * @property int|null $goal_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Objective extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function (Objective $obj) {
            do {
                // random ref
                $ref = 'OBJ' . substr(
                        str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),
                        0, 8);

            } while(Objective::where('reference', $ref)->exists());

            $obj->reference = $ref;
        });
    }

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'trackable'
    ];

    /*
     * Morph objectives to other models
     */
    public function objectivable():MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the trackable associated with the objective.
     *
     * @return MorphOne
     */
    public function trackable(): MorphOne
    {
        return $this->morphOne(Trackable::class, 'trackable');
    }

    /**
     * Get the goal associated with the objective.
     *
     * @return BelongsTo
     */
    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    /**
     * Get the daily plan associated with the activity, if any.
     *
     * @return BelongsTo
     */
    public function dailyPlan(): BelongsTo
    {
        return $this->belongsTo(DailyPlan::class, 'dailyplannable_id')
            ->where('dailyplannable_type', DailyPlan::class);
    }
}
