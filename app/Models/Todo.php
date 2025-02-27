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
 * Class To-do
 *
 * Represents a to-do item that can be tracked and associated with tasks and projects.
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
 * @property int|null $project_id
 * @property int|null $task_id
 * @property Carbon|null $from
 * @property Carbon|null $to
 * @property Carbon|null $due_at
 * @property Carbon|null $archived_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Todo extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function (Todo $todo) {
            do {
                // random ref
                $ref = 'TOD' . substr(
                        str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),
                        0, 8);

            } while(Todo::where('reference', $ref)->exists());

            $todo->reference = $ref;
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
     * Get the trackable associated with the to-do.
     *
     */
    public function trackable(): MorphOne
    {
        return $this->morphOne(Trackable::class, 'trackable');
    }


    /**
     * Get the goal associated with the to-do.
     *
     * @return BelongsTo
     */
    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }


    /**
     * Get the project associated with the to-do.
     *
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the task associated with the to-do
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the task associated with the to-do.
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
