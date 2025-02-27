<?php

namespace Database\Factories;

use App\Models\Visibility;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visibility>
 */
class VisibilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vis = ['private', 'public', 'shared', 'team'];
        return [
            'name' => $title=Arr::random($vis),
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(5, false)
        ];
    }
}
