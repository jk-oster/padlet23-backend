<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'content',
        'cover',
        'color',
        'user_id',
        'padlet_id',
    ];

    protected $appends = ['rating'];

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

    public function getRatingAttribute() : array
    {
        $ratings = $this->ratings();
        $count = $ratings->count();
        $totalRating = (int)($ratings->sum('rating') ?? 0);
        $userId = \App\Models\User::getUserIdOrPublic();
        $userRating = $ratings->where('user_id', $userId)->first();

        return [
            'rating' =>  $totalRating,
            'count' => $count ?? 0,
            'user_rating' => $userRating->rating ?? 0,
            'user_rating_id' => $userRating->id ?? 0,
        ];
    }

}
