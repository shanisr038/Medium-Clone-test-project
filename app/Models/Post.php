<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'content',
        'slug',
        'category_id',
        'user_id',
        'published_at',
    ];

    /**
     * Register the media collections for Spatie Media Library
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile()
            ->useFallbackUrl('/images/default.jpg'); // optional fallback
    }

    /**
     * Helper to return the image URL
     */
    public function imageUrl(): ?string
    {
        return $this->getFirstMediaUrl('image');
    }

    /**
     * Count reading time
     */
    public function readtime(int $wordsPerMinute = 100): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, (int) ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function claps(): HasMany
    {
        return $this->hasMany(Clap::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)
            ->with('user', 'replies');
    }

    /**
     * Check if a user clapped the post
     */
    public function isClappedBy($user): bool
    {
        if (!$user) return false;
        return $this->claps()
            ->where('user_id', $user->id)
            ->exists();
    }
}
