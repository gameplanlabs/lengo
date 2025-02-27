<?php

namespace App\Models;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use App\Services\SlugGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class DailyTarget
 *
 * Represents a daily target for a user, allowing them to target activities,
 * for a specific time-block in a day.
 *
 * @property int $id
 * @property string $reference
 * @property string $title
 * @property string $slug
 * @property string $description
 * @property string $status
 * @property string $priority
 * @property int $progress
 * @property int $daily_plan_id
 * @property int $todo_id
 * @property int $user_id
 * @property int|null $trackable_id
 * @property int|null $dailyplannable_id
 * @property string|null $dailyplannable_type
 * @property string|null $review
 * @property Carbon|null $due_at
 * @property Carbon|null $from
 * @property Carbon|null $to
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $archived_at
 * @property Carbon|null $deleted_at
 */
class DailyTarget extends Model
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
        static::creating(function (DailyTarget $dailyTarget) {
            do {
                // Generate a unique reference for the daily plan
                $ref = 'DAT_' . substr(
                        str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),
                        0, 8);

            } while (DailyTarget::where('reference', $ref)->exists());

            $dailyTarget->reference = $ref;

            // generate unique slug
            $dailyTarget->slug = SlugGenerator::generate($dailyTarget->title, $dailyTarget);
        });
    }

    protected $casts = [
        'from' => 'datetime',
        'to' => 'datetime',
        'due_at' => 'datetime',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'status' => TrackableStatus::class,
        'priority' => TrackablePriority::class,
    ];

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
     * Get the dailyPlan associated w/this.
     *
     * @return BelongsTo
     */
    public function dailyPlan(): BelongsTo
    {
        return $this->belongsTo(DailyPlan::class);
    }

    /**
     * Get the todo associated w/this.
     *
     * @return BelongsTo
     */
    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
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
     * Get the dailytargetable entity associated with the daily plan.
     * This can be a Task, To-do, Activity, etc.
     *
     * @return MorphTo
     */
    public function dailytargetable(): MorphTo
    {
        return $this->morphTo();
    }

    /*
     * Activities associated with this target
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
