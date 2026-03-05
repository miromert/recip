<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Step extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'recipe_id',
        'order',
        'instruction',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
