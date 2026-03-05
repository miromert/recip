<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class KnownIngredient extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'brand',
        'calories_per_100g',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'calories_per_100g' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (KnownIngredient $ingredient) {
            if (empty($ingredient->slug)) {
                $ingredient->slug = Str::slug($ingredient->name);
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    /**
     * How many recipes use this ingredient.
     */
    public function getRecipeCountAttribute(): int
    {
        return $this->ingredients()->distinct('recipe_id')->count('recipe_id');
    }
}
