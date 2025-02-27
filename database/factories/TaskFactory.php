<?php

namespace Database\Factories;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Project;
use App\Models\Trackable;
use App\Models\User;
use App\Models\Visibility;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
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
            'estimated_time' => rand(22,30),
            'actual_time' => $acl=rand(23,26),
//            'goal_id' => Goal::factory(),
            'project_id' => Project::factory(),
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

            'from' => $fr = now()->addUTCDays(rand(-20, 20)),
            'to' => $to=$fr->addUTCDays($acl),
            'due_at' => $to,
            'completed_at' => $to,
            'progress' => $this->faker->randomFloat(2, 0.01, 1),
        ];
    }
}
