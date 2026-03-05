<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Recipe extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'prep_time_minutes',
        'cook_time_minutes',
        'rest_time_minutes',
        'total_time_minutes',
        'servings',
        'yield',
        'difficulty',
        'image_path',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'prep_time_minutes' => 'integer',
            'cook_time_minutes' => 'integer',
            'rest_time_minutes' => 'integer',
            'total_time_minutes' => 'integer',
            'servings' => 'integer',
        ];
    }

    /**
     * Boot the model — auto-generate slug and compute total time.
     */
    protected static function booted(): void
    {
        static::creating(function (Recipe $recipe) {
            if (empty($recipe->slug)) {
                $recipe->slug = static::generateUniqueSlug($recipe->title);
            }
            $recipe->computeTotalTime();
        });

        static::updating(function (Recipe $recipe) {
            $recipe->computeTotalTime();
        });
    }

    /**
     * Generate a unique slug from a title.
     */
    public static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Compute total_time_minutes from prep + cook + rest.
     */
    public function computeTotalTime(): void
    {
        $this->total_time_minutes = ($this->prep_time_minutes ?? 0)
            + ($this->cook_time_minutes ?? 0)
            + ($this->rest_time_minutes ?? 0);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // --- Relationships ---

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class)->orderBy('order');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class)->orderBy('order');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    // --- Scopes ---

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePopular($query)
    {
        return $query->withCount('votes')->orderByDesc('votes_count');
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('published_at');
    }

    // --- Accessors ---

    /**
     * Format time as "X hr Y min" or "Y min".
     */
    public function getFormattedTotalTimeAttribute(): string
    {
        $minutes = $this->total_time_minutes;
        if (!$minutes) {
            return 'N/A';
        }

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours} hr {$mins} min";
        } elseif ($hours > 0) {
            return "{$hours} hr";
        }

        return "{$mins} min";
    }

    public function getFormattedPrepTimeAttribute(): string
    {
        return $this->formatMinutes($this->prep_time_minutes);
    }

    public function getFormattedCookTimeAttribute(): string
    {
        return $this->formatMinutes($this->cook_time_minutes);
    }

    private function formatMinutes(?int $minutes): string
    {
        if (!$minutes) {
            return 'N/A';
        }

        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours} hr {$mins} min";
        } elseif ($hours > 0) {
            return "{$hours} hr";
        }

        return "{$mins} min";
    }
}
