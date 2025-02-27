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
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trackable>
 */
class TrackableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = '-' . rand(1,3) . Arr::random([' months', ' weeks', ' days', ' hours']);
        $endDate = '+' . rand(1,3) . Arr::random([' months', ' weeks', ' days', ' hours']);
        $timezone = "Africa/Nairobi";

        return [
            'previous_pause_at' => $this->faker->optional(0.3)->dateTime, // Optional previous resume date
            'pause_count' => $this->faker->optional(0.3)->numberBetween(0, 10), // Optional pause count
            'started_at' => $this->faker->dateTimeBetween($startDate, $endDate, $timezone),
            'ended_at' => $this->faker->dateTimeBetween($startDate, $endDate, $timezone),
            'paused_at' => $this->faker->dateTimeBetween($startDate, $endDate, $timezone),
            'resumed_at' => $this->faker->dateTimeBetween($startDate, $endDate, $timezone),

            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'visibility_id' => Visibility::factory(),
            'trackable_id' => null, // Set to null or use a factory for polymorphic relation
            'trackable_type' => null, // Set to null or use a factory for polymorphic relation
            'archived_at' => $this->faker->optional(0.1)->dateTime,
        ];
    }
}
