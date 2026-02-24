<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name'];

    /**
     * Get the posts for the category.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get cached categories list.
     */
    public static function getCached(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('categories_list', now()->addHours(24), function () {
            return static::orderBy('name')->get();
        });
    }

    /**
     * Clear categories cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('categories_list');
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function () {
            static::clearCache();
        });

        static::updated(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }
}
