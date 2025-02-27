<?php

namespace Database\Factories;

use App\Enums\TrackablePriority;
use App\Enums\TrackableStatus;
use App\Models\Trackable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyPlan>
 */
class DailyPlanFactory extends Factory
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
            'excerpt' => $this->faker->optional()->paragraph,
            'description' => $this->faker->optional()->text,
            'the_day' => $td=$this->faker->dateTimeBetween('now', '+1 day'), // Ideally tomorrow
            'user_id' => User::factory(), // Assuming you have a User factory
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

            'from' => $fr = now()->parse($td),
            'to' => $to=$fr->addHours(rand(1,2)),
            'progress' => $this->faker->randomFloat(2, 0.01, 1),
            'started_at' => $fr,
            'paused_at' => $fr->addHour(),
            'ended_at' => $to,
            'dailyplannable_id' => null, // Set to null or use a factory for polymorphic relation
            'dailyplannable_type' => null, // Set to null or use a factory for polymorphic relation
            'review' => $this->faker->optional()->text,
        ];
    }
}
