<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public CONST PUBLIC_USER_ID = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'role',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* ---- auth JWT ---- */
    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return ['user' => ['id' => $this->id]];
    }

    public function isAdmin() : bool
    {
        return $this->role === 'admin';
    }

    public function isPublicUser() : bool
    {
        return $this->id === self::PUBLIC_USER_ID;
    }

    public static function getUserIdOrPublic() : int
    {
        $user = auth()->user();
        return $user ? $user->id : self::PUBLIC_USER_ID;
    }

    /* ---- relations ---- */

    /**
     * user has many padlets (1:n)
     * @return HasMany
     */
    public function padlets() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Padlet::class);
    }

    /**
     * user has many posts (1:n)
     * @return HasMany
     */
    public function posts() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * user has many comments (1:n)
     * @return HasMany
     */
    public function comments() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * user has many ratings (1:n)
     * @return HasMany
     */
    public function ratings() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * user has many padletUsers (1:n)
     * @return BelongsToMany
     */
    public function padletUser() : \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Padlet::class)->withPivot('permission_level', 'accepted');
    }


    public function sharedPadlets() : \Illuminate\Support\Collection
    {
        $padletUsers = $this->padletUser()->where('accepted', true);
        return $padletUsers->get()->map(static function ($padletUser) {
            return $padletUser->padlet;
        });
    }

    public function pendingPadlets() : \Illuminate\Support\Collection
    {
        $padletUsers = $this->pendingPadletUser();
        return $padletUsers->get()->map(static function ($padletUser) {
            return $padletUser->padlet;
        });
    }

    public function pendingPadletUser() : \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->padletUser()->where('accepted', false);
    }
}
