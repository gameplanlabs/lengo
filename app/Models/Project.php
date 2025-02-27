<?php

namespace App\Models;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use App\Services\SlugGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Project
 *
 * Represents a project that can be tracked and associated with goals and tasks.
 *
 * @property int $id
 * @property string $reference
 * @property string $title
 * @property string $slug
 * @property string $excerpt
 * @property string $description
 * @property string $status
 * @property string $priority
 * @property int $estimated_time
 * @property int $actual_time
 * @property int|null $goal_id
 * @property int|null $objective_id
 * @property int $progress
 * @property float|null $budget
 * @property float|null $spent_budget
 * @property Carbon|null $due_at
 * @property Carbon|null $from
 * @property Carbon|null $to
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $archived_at
 * @property Carbon|null $deleted_at
 */
class Project extends Model
{
    use HasFactory, SoftDeletes;

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
        static::creating(function (Project $project) {
            do {
                // random ref
                $ref = 'PRJ' . substr(
                        str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),
                        0, 8);

            } while(Project::where('reference', $ref)->exists());

            $project->reference = $ref;

            // generate unique slug
            $project->slug = SlugGenerator::generate($project->title, $project);
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
     * Get the trackable associated with the project.
     *
     * @return MorphOne
     */
    public function trackable(): MorphOne
    {
        return $this->morphOne(Trackable::class, 'trackable');
    }

    /**
     * Get the user associated with the project.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the goal associated with the project.
     *
     * @return BelongsTo
     */
    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    /**
     * Get the tasks associated with the project.
     *
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the todos associated with the project.
     *
     * @return HasMany
     */
    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    /**
     * Get the activities associated with the project.
     *
     * @return HasMany
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the daily targets associated with the project.
     *
     * @return HasMany
     */
    public function dailyTargets(): HasMany
    {
        return $this->hasMany(DailyTarget::class);
    }
}
