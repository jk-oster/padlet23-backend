<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'rating',
        'user_id',
        'post_id',
    ];

    // rating belongs to one user (n:1) and one post (n:1)
    public function user() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function scopeByPostId($query, $postId) : \Illuminate\Database\Eloquent\Builder
    {
        $userId = \App\Models\User::getUserIdOrPublic();
        return $query->where('post_id', $postId)
            ->where('user_id', $userId);
    }
}
