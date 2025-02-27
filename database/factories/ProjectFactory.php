<?php

namespace Database\Factories;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use App\Models\Goal;
use App\Models\Objective;
use App\Models\Trackable;
use App\Models\User;
use App\Models\Visibility;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
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
            'excerpt' => $this->faker->optional()->paragraph, // Optional excerpt
            'description' => $this->faker->optional()->text, // Optional description

            'goal_id' => Goal::factory(),
            'user_id' => User::factory(),
            'estimated_time' => Arr::random([300, 360]),
            'actual_time' => $acl=Arr::random([200, 312]),

            'status' => Arr::random([
                TrackableStatus::PLANNING,
                TrackableStatus::ACTIVE,
                TrackableStatus::COMPLETED,
                TrackableStatus::PAUSED,
                TrackableStatus::CANCELLED
            ]), // Random status
            'priority' => Arr::random([
                TrackablePriority::LOW,
                TrackablePriority::MEDIUM,
                TrackablePriority::HIGH
            ]), // Random priority

            'from' => $fr = now()->addUTCMonths(rand(-6, 3)),
            'to' => $to=$fr->addUTCMonths($acl),
            'due_at' => $to,
            'completed_at' => $to,
            'progress' => $this->faker->randomFloat(2, 0.01, 1),

            'budget' => $this->faker->optional()->randomFloat(2, 1000, 10000), // Optional budget
            'spent_budget' => $this->faker->optional()->randomFloat(2, 0, 10000), // Optional spent budget
        ];
    }
}
