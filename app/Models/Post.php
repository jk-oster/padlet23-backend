<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'content',
        'cover',
        'user_id',
        'padlet_id',
    ];

    protected $appends = ['rating', 'ratings_count', 'comments_count', 'user_rating'];

    /**
     * post belongs to one user (n:1)
     */
    public function user() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * post belongs to one padlet (n:1)
     */
    public function padlet() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Padlet::class);
    }

    /**
     * post has many ratings (1:n)
     */
    public function ratings() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * post has many comments (1:n)
     */
    public function comments() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function isOwner(User $user) : bool
    {
        return $this->user_id === $user->id;
    }

    public function scopeByPadletId($query, $padletId) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('padlet_id', $padletId);
    }

    public function getRatingAttribute() : int
    {
        return $this->ratings()->sum('rating') ?? 0;
    }

    public function getRatingsCountAttribute() : int
    {
        return $this->ratings()->count() ?? 0;
    }

    public function getCommentsCountAttribute() : int
    {
        return $this->comments()->count() ?? 0;
    }

    public function getUserRatingAttribute() : int
    {
        $user = auth()->user();
        if ($user) {
            $rating = $this->ratings()->where('user_id', $user->id)->first();
            if ($rating) {
                return $rating->rating;
            }
        }
        return 0;
    }

}
