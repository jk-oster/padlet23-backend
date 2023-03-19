<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Padlet extends Model
{
    use HasFactory;

    public const PERMISSION_LEVELS = [
        'view' => 1,
        'comment' => 2,
        'edit' => 3,
        'admin' => 4,
    ];
    protected $fillable = [
        'name',
        'cover',
        'public',
        'user_id',
    ];

    /**
     * padlet belongs to one user (n:1)
     */
    public function user() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * padlet has many posts (1:n)
     */
    public function posts() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * padlet has many padletUsers (1:n)
     */
    public function padletUser() : \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('permission_level', 'accepted');
    }

    public function acceptedPadletUsers() : \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->padletUser()->where('accepted', true);
    }

    public function pendingPadletUsers() : \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->padletUser()->where('accepted', false);
    }

    public function canEdit(User $user) : bool
    {
        return $this->isPublic() || $this->isOwner($user) || $this->acceptedPadletUsers()
            ->where('user_id', $user->id)
            ->where('permission_level', '>=', self::PERMISSION_LEVELS['edit'])
            ->exists();
    }

    public function canComment(User $user) : bool
    {
        return $this->isPublic() || $this->isOwner($user) || $this->acceptedPadletUsers()
            ->where('user_id', $user->id)
            ->where('permission_level', '>=', self::PERMISSION_LEVELS['comment'])
            ->exists();
    }

    public function canView(User $user) : bool
    {
        return $this->isPublic() || $this->isOwner($user) || $this->acceptedPadletUsers()
            ->where('user_id', $user->id)
            ->where('permission_level', '>=', self::PERMISSION_LEVELS['view'])
            ->exists();
    }

    public function isOwner(User $user) : bool
    {
        return $this->user_id === $user->id;
    }

    public function isMember(User $user) : bool
    {
        return $this->acceptedPadletUsers()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function isPublic() : bool
    {
        return $this->public;
    }

    public static function scopePublicPadlets($query) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('public', true);
    }

    public static function scopeSharedPadlets($query, User $user) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('public', false)->where('user_id', '!=', $user->id)->whereHas('padletUser', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('accepted', true);
        });
    }

    public static function scopePrivatePadlets($query, User $user) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('public', false)->where('user_id', $user->id);
    }
}
