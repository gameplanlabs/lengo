<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class DailyPlan
 *
 * Represents a daily plan for a user, allowing them to schedule activities,
 * tasks, or other entities for a specific day.
 *
 * @property int $id
 * @property string $reference
 * @property string|null $the_day
 * @property int $user_id
 * @property int|null $trackable_id
 * @property int|null $dailyplannable_id
 * @property string|null $dailyplannable_type
 * @property string|null $review
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class DailyPlan extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The "booted" method is called when the model is being created.
     * This method generates a unique reference for the daily plan.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function (DailyPlan $dailyPlan) {
            do {
                // Generate a unique reference for the daily plan
                $ref = 'DAP' . substr(
                        str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),
                        0, 8);

            } while (DailyPlan::where('reference', $ref)->exists());

            $dailyPlan->reference = $ref;
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

    /**
     * Get the user that owns the daily plan.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the trackable associated with the daily plan.
     *
     * @return MorphOne
     */
    public function trackable(): MorphOne
    {
        return $this->morphOne(Trackable::class, 'trackable');
    }

    /**
     * Get the dailyplannable entity associated with the daily plan.
     * This can be a Task, To-do, Activity, etc.
     *
     * @return MorphTo
     */
    public function dailyplannable(): MorphTo
    {
        return $this->morphTo();
    }
}
