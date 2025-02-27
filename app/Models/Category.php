<?php

namespace App\Models;

use App\Services\SlugGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property $reference
 * @property $name
 * @property $id
 * @property $slug
 * @property $excerpt
 * @property $description
 */
class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function (Category $category) {
            do {
                // random ref
                $ref = 'CAT' . substr(
                        str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),
                        0, 8);

            } while(Category::where('reference', $ref)->exists());

            $category->reference = $ref;

            // generate unique slug
            $category->slug = SlugGenerator::generate($category->name, $category);
        });
    }

    public function trackables(): HasMany
    {
        return $this->hasMany(Trackable::class);
    }

}
