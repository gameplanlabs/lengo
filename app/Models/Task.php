<?php

namespace App\Models;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
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
 * @property int|null $project_id
 * @property int|null $progress
 * @property int|null $estimated_time
 * @property int|null $actual_time
 * @property Carbon|null $from
 * @property Carbon|null $to
 * @property Carbon|null $due_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $archived_at
 * @property Carbon|null $deleted_at
 */
class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

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

    protected static function booted()
    {
        static::creating(function (Task $task) {
            do {
                // random ref
                $ref = 'TAS' . substr(
                        str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),
                        0, 8);

            } while(Task::where('reference', $ref)->exists());

            $task->reference = $ref;
        });
    }

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
     * Get the trackable associated with the task.
     *
     * @return MorphOne
     */
    public function trackable(): MorphOne
    {
        return $this->morphOne(Trackable::class, 'trackable');
    }

    /**
     * Get the project associated with the task.
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user associated with the task.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the goal associated with the task.
     *
     * @return BelongsTo
     */
    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    /**
     * Get the todos associated with the task.
     *
     * @return HasMany
     */
    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    /**
     * Get the activities associated with the task.
     *
     * @return HasMany
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the daily plan associated with the activity, if any.
     *
     * @return BelongsTo
     */
    public function dailyPlan(): BelongsTo
    {
        return $this->belongsTo(DailyPlan::class, 'dailyplannable_id')->where('dailyplannable_type', DailyPlan::class);
    }
}
