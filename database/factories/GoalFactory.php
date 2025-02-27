<?php

namespace Database\Factories;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use App\Models\Category;
use App\Models\Trackable;
use App\Models\User;
use App\Models\Visibility;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Goal>
 */
class GoalFactory extends Factory
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
            'excerpt' => $this->faker->optional(0.8)->paragraph, // Optional excerpt
            'description' => $this->faker->optional(0.75)->text, // Optional description
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

            'user_id' => User::factory(),
            'from' => $fr = now()->addUTCMonths(rand(-18,26)),
            'to' => $to=$fr->addUTCMonths(rand(6,42)),
            'due_at' => $to,
            'completed_at' => $to,
            'progress' => $this->faker->randomFloat(2, 0.01, 1)
        ];
    }
}
