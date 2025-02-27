<?php

namespace App\Models;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class Trackable
 *
 * Represents a trackable entity that can be associated with various models
 * such as goals, projects, tasks, todos, and activities.
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $excerpt
 * @property string $description
 * @property string $status
 * @property string $priority
 * @property string $starts_at
 * @property string $ends_at
 * @property string $due_at
 * @property string $started_at
 * @property string $ended_at
 * @property string $paused_at
 * @property string $deleted_at
 * @property int $user_id
 * @property int $category_id
 * @property int $visibility_id
 * @property int $trackable_id
 * @property string|null $trackable_type
 * @property mixed $trackable
 */
class Trackable extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'from' => 'datetime',
        'to' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'paused_at' => 'datetime',
        'deleted_at' => 'datetime',
        'status' => TrackableStatus::class,
        'priority' => TrackablePriority::class,
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'type',
    ];

    protected $with = [
        'category', 'visibility', 'user'
    ];

    /**
     * Get the parent trackable model (goal, project, etc.)
     *
     * @return MorphTo
     */
    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the trackable.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category associated with the trackable.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the visibility associated with the trackable.
     *
     * @return BelongsTo
     */
    public function visibility(): BelongsTo
    {
        return $this->belongsTo(Visibility::class);
    }

    /**
     * Get the type of the trackable model.
     *
     * @return string|null
     */
    public function getTypeAttribute(): ?string
    {
        // Get the value of the trackable_type column
        $trackableType = $this->attributes['trackable_type'];

        return match ($trackableType) {
            Goal::class => 'goal',
            Project::class => 'project',
            Task::class => 'task',
            Todo::class => 'todo',
            Objective::class => 'objective',
            Activity::class => 'activity',
            DailyPlan::class => 'daily plan',
            DailyTarget::class => 'daily target',
            default => null,
        };
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
}
