<?php

namespace Database\Factories;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use App\Models\DailyPlan;
use App\Models\DailyTarget;
use App\Models\Project;
use App\Models\Task;
use App\Models\Todo;
use App\Models\Trackable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->optional()->text, // Optional description
            'progress' => $this->faker->randomFloat(2, 0.05, 1),
            'estimated_time' => rand(1, 3),
            'actual_time' => $acl = rand(1, 3),
            'user_id' => User::factory(),
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

            'from' => $fr = now()->addHours(rand(-1000, 2500)),
            'to' => $fr->addHours($acl),
            'due_at' => $fr->addHours($acl),
            'completed_at' => $fr->addHours($acl),

//            'goal_id' => Project::factory(),
//            'project_id' => Project::factory(),
//            'task_id' => Task::factory(),
//            'daily_plan_id' => DailyPlan::factory(),
//            'daily_target_id' => DailyTarget::factory(),
        ];
    }
}
