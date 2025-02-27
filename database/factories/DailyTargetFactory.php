<?php

namespace Database\Factories;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use App\Models\DailyPlan;
use App\Models\Todo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyTarget>
 */
class DailyTargetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $title = $this->faker->sentence,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->optional()->paragraph,
            'description' => $this->faker->optional()->text,

            'daily_plan_id' => DailyPlan::factory(),
            'todo_id' => Todo::factory(),

            'status' => Arr::random([
                TrackableStatus::PLANNING,
                TrackableStatus::ACTIVE,
                TrackableStatus::COMPLETED,
                TrackableStatus::PAUSED,
                TrackableStatus::INPROGRESS
            ]), // Random status
            'priority' => Arr::random([
                TrackablePriority::LOW,
                TrackablePriority::MEDIUM,
                TrackablePriority::HIGH
            ]), // Random priority

            'from' => $fr = now()->addDays(rand(-15, 15)),
            'to' => $to=$fr->addHours(rand(3,8)),
            'due_at' => $to,
            'completed_at' => $to,
            'progress' => $this->faker->randomFloat(2, 0.01, 1),
            'started_at' => $fr,
            'paused_at' => $fr->addHour(),
            'resumed_at' => $fr->addHours(2),
            'ended_at' => $to->addHour(),
            'daily_targetable_id' => null, // Set to null or use a factory for polymorphic relation
            'daily_targetable_type' => null, // Set to null or use a factory for polymorphic relation
        ];
    }
}
