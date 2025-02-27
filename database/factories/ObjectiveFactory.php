<?php

namespace Database\Factories;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use App\Models\Goal;
use App\Models\Trackable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Objective>
 */
class ObjectiveFactory extends Factory
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
            'description' => $this->faker->paragraph(5),
            'goal_id' => Goal::factory(),
            'user_id' => User::factory(),

            'status' => Arr::random([
                TrackableStatus::PLANNING,
                TrackableStatus::ACTIVE,
                TrackableStatus::COMPLETED,
                TrackableStatus::PAUSED,
                TrackableStatus::ACHIEVED
            ]), // Random status
            'priority' => Arr::random([
                TrackablePriority::LOW,
                TrackablePriority::MEDIUM,
                TrackablePriority::HIGH
            ]), // Random priority
        ];
    }
}
