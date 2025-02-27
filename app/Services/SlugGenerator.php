<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SlugGenerator
{
    /**
     * Generates a unique slug.
     * @param string $title
     * @param Model|null $model
     * @return string
     */
    public static function generate(string $title, Model $model = null): string
    {
        if (is_null($model)) {
            return Str::slug($title);
        }
        $slug = Str::slug($title);
        $existingSlug = $model::where('slug', $slug)->first();

        do {
            $randString = Str::random(rand(4,8));
            $slug = $slug. '-'. $randString;
        } while ($existingSlug);

        return $slug;
    }
}
