<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia;

    protected $fillable = [
        'name',
        'username',
        'email',
        'bio',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // =============================
    // 🔗 RELATIONSHIPS
    // =============================

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function followers()
    {
        return $this->hasMany(Follower::class, 'user_id');
    }

    public function following()
    {
        return $this->hasMany(Follower::class, 'follower_id');
    }

    // =============================
    // 🧩 MEDIA LIBRARY
    // =============================

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile(); // Only store one avatar per user
    }

    // =============================
    // 🧠 HELPERS
    // =============================

    public function avatarUrl(): string
    {
        // Get media from avatar collection
        $media = $this->getFirstMedia('avatar');

        return $media
            ? $media->getUrl()
            : asset('images/default-avatar.png');
    }

    public function isFollowedBy(?User $user): bool
    {
        if (!$user) return false;

        return $this->followers()
            ->where('follower_id', $user->id)
            ->exists();
    }

    public function followersCount(): int
    {
        return $this->followers()->count();
    }

    public function followingCount(): int
    {
        return $this->following()->count();
    }
}
