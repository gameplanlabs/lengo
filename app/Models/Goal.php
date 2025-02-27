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
 * Class Goal
 *
 * Represents a goal that can be tracked and associated with projects and objectives.
 *
 * @property int $id
 * @property string $reference
 * @property string $title
 * @property string $slug
 * @property string $excerpt
 * @property string $description
 * @property string $progress
 * @property string $priority
 * @property string $status
 * @property Carbon|null $from
 * @property Carbon|null $to
 * @property Carbon|null $due_at
 * @property Carbon|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $archived_at
 * @property Carbon|null $deleted_at
 */
class Goal extends Model
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
        static::creating(function (Goal $goal) {
            do {
                // Generate a unique reference for the daily plan
                $ref = 'GOA_' . substr(
                        str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),
                        0, 8);

            } while (Goal::where('reference', $ref)->exists());

            $goal->reference = $ref;

            // generate unique slug
            $goal->slug = SlugGenerator::generate($goal->title, $goal);
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
        'trackable', 'objectives'
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
     * Get the trackable associated with the goal.
     *
     * @return MorphOne
     */
    public function trackable(): MorphOne
    {
        return $this->morphOne(Trackable::class, 'trackable');
    }

    /**
     * Get the projects associated with the goal.
     *
     * @return HasMany
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get the objectives associated with the goal.
     *
     * @return HasMany
     */
    public function objectives(): HasMany
    {
        return $this->hasMany(Objective::class);
    }

    /**
     * Get the activities associated with the goal.
     *
     * @return HasMany
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the tasks associated with the goal.
     *
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the to-dos associated with the goal.
     *
     * @return HasMany
     */
    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    /**
     * Get the daily plans tracking this goal
     *
     * @return HasMany
     */
    public function dailyPlans(): HasMany
    {
        return $this->hasMany(DailyPlan::class);
    }

    /**
     * Get the daily targets tracking this goal
     *
     * @return HasMany
     */
    public function dailyTargets(): HasMany
    {
        return $this->hasMany(DailyTarget::class);
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
