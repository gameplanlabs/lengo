<?php

namespace Database\Factories;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Project;
use App\Models\Task;
use App\Models\Trackable;
use App\Models\User;
use App\Models\Visibility;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
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
            'estimated_time' => rand(4,12),
            'actual_time' => $acl = rand(4,12),
//            'goal_id' => Goal::factory(),
//            'project_id' => Project::factory(),
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
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

            'from' => $fr = now()->addUTCHours(rand(72, 120)),
            'to' => $to=$fr->addHours($acl),
            'due_at' => $to,
            'completed_at' => $to,
            'progress' => $this->faker->randomFloat(2, 0.01, 1)
        ];
    }
}
