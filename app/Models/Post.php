<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = [
        'category_id', 'user_id', 'title', 'slug', 'summary',
        'content', 'image_url', 'rss_source', 'original_link',
        'views_count', 'is_published', 'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFilter(Builder $query, array $filters = []): Builder
    {
        $query->published();

        $keyword = trim((string) ($filters['keyword'] ?? ''));
        if ($keyword !== '') {
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $keyword);
            $pattern = '%' . $escaped . '%';

            $query->where(function (Builder $q) use ($pattern): void {
                $q->where('title', 'like', $pattern)
                    ->orWhere('summary', 'like', $pattern);
            });
        }

        $category = $filters['category'] ?? null;
        if (filled($category)) {
            if (is_numeric($category)) {
                $query->where('category_id', (int) $category);
            } else {
                $query->whereHas('category', fn (Builder $q) => $q->where('slug', (string) $category));
            }
        }

        return match ($filters['sort'] ?? 'latest') {
            'oldest' => $query->orderByRaw('COALESCE(published_at, created_at) ASC')->orderBy('id'),
            'popular', 'views_count' => $query->orderByDesc('views_count')->orderByDesc('id'),
            default => $query->orderByRaw('COALESCE(published_at, created_at) DESC')->orderByDesc('id'),
        };
    }
}