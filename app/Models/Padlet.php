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

    protected $appends = ['posts_count'];


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

    public function isPrivate() : bool
    {
        return !$this->public;
    }

    public function scopeAccessiblePadlets($query, $user) : \Illuminate\Database\Eloquent\Builder
    {
        if ($user instanceof User) {

            if ($user->isAdmin()) {
                return $query;
            }

            return $query
                ->where('public', true) // public padlets
                ->orWhere('user_id', $user->id) // user is owner
                ->orWhereHas('padletUser', function ($query) use ($user) {
                    // user is member and accepted invitation
                    $query->where('user_id', $user->id)->where('accepted', true);
                });
        }
        return $query->where('public', true);
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

    public function getPostsCountAttribute() : int
    {
        return $this->posts()->count();
    }
}
