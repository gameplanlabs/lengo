<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cats = [
            'Programming', 'Farming', 'Machine Learning', 'Learning',
            'Teaching', 'Writing', 'Business', 'Science', 'Engineering'
        ];

        $name = Arr::random($cats);// . ' ' . $this->faker->word();
        $slug = Str::slug($name);
        return [
            'name' => $name,
            'slug' => $this->uniqueSlug($slug),
            'excerpt' => $this->faker->sentences(2, true),
            'description' => $this->faker->paragraph(5, false)
        ];
    }

    protected function uniqueSlug($slug)
    {
        $originalSlug = $slug;
        $count = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}
