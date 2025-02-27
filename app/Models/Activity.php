<?php

namespace App\Models;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Activity
 *
 * Represents a specific activity that can be associated with a trackable item,
 * a project, a task, and a to-do.
 *
 * @property int $id
 * @property string $reference
 * @property string $title
 * @property string $description
 * @property string $status
 * @property string $priority
 * @property int $progress
 * @property int $estimated_time
 * @property int $actual_time
 * @property int $trackable_id
 * @property int|null $project_id
 * @property int|null $task_id
 * @property int|null $todo_id
 * @property Carbon|null $from
 * @property Carbon|null $to
 * @property Carbon|null $due_at
 * @property Carbon|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $archived_at
 * @property Carbon|null $deleted_at
 */
class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /**
     * The "booted" method is called when the model is being created.
     * This method generates a unique reference for the daily plan.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function (Activity $activity) {
            do {
                // Generate a unique reference for the daily plan
                $ref = 'ACT_' . substr(
                        str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),
                        0, 8);

            } while (Activity::where('reference', $ref)->exists());

            $activity->reference = $ref;
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

    /*
     * Eagerloaded relations
     */
    protected $with = [
        'trackable'
    ];

    // Accessor for 'from' to format it as a time string
    public function getFromAttribute($value): string
    {
        return Carbon::parse($value)->format('h:i A'); // Format as '08:00 AM'
    }

    // Accessor for 'to' to format it as a time string
    public function getToAttribute($value): string
    {
        return Carbon::parse($value)->format('h:i A'); // Format as '08:00 AM'
    }

    /**
     * Get the trackable associated with the activity.
     *
     * @return MorphOne
     */
    public function trackable(): MorphOne
    {
        return $this->morphOne(Trackable::class, 'trackable');
    }

    /**
     * Get the goal associated with the activity.
     *
     * @return BelongsTo
     */
    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    /**
     * Get the project associated with the activity.
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the task associated with the activity.
     *
     * @return BelongsTo
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the "to-do" associated with the activity.
     *
     * @return BelongsTo
     */
    public function todo(): BelongsTo
    {
        return $this->belongsTo(Todo::class);
    }

    /**
     * Get the daily plan associated with the activity, if any.
     *
     * @return BelongsTo
     */
    public function dailyPlan(): BelongsTo
    {
        return $this->belongsTo(DailyPlan::class);
    }

    /**
     * Get the daily target associated with the activity, if any.
     *
     * @return BelongsTo
     */
    public function dailyTarget(): BelongsTo
    {
        return $this->belongsTo(DailyTarget::class);
    }
}
