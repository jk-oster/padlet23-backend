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

    public function isOwner(User $user) : bool
    {
        return $this->user_id === $user->id;
    }

    public function scopeByPadletId($query, $padletId) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('padlet_id', $padletId);
    }
}
