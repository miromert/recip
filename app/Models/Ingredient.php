<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingredient extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'recipe_id',
        'known_ingredient_id',
        'amount',
        'unit',
        'name',
        'group',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'order' => 'integer',
        ];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function knownIngredient(): BelongsTo
    {
        return $this->belongsTo(KnownIngredient::class);
    }

    /**
     * Format ingredient as a readable string, e.g. "1.5 cups all-purpose flour".
     */
    public function getFormattedAttribute(): string
    {
        $parts = [];

        if ($this->amount) {
            // Clean trailing zeros: "1.00" -> "1", "1.50" -> "1.5"
            $amount = rtrim(rtrim(number_format($this->amount, 2), '0'), '.');
            $parts[] = $amount;
        }

        if ($this->unit) {
            $parts[] = $this->unit;
        }

        $parts[] = $this->name;

        return implode(' ', $parts);
    }
}
