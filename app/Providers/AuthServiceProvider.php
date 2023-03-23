<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Padlet;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(static function (?User $user, $ability) {
            if ($user && $user->isAdmin()) {
                return true;
            }
        });

        Gate::define('view', static function (?User $user, Padlet $padlet) {
            return self::hasPermission(Padlet::PERMISSION_LEVELS['view'], $padlet, $user);
        });

        Gate::define('comment', static function (?User $user, Padlet $padlet) {
            return self::hasPermission(Padlet::PERMISSION_LEVELS['comment'], $padlet, $user);
        });

        Gate::define('edit', static function (?User $user, Padlet $padlet) {
            return self::hasPermission(Padlet::PERMISSION_LEVELS['edit'], $padlet, $user);
        });

        Gate::define('admin', static function (?User $user, Padlet $padlet) {
            return self::hasPermission(Padlet::PERMISSION_LEVELS['admin'], $padlet, $user);
        });

        // Gates that defines that a user can only delete his own comments & ratings
        Gate::define('edit-delete-comment', static function (?User $user, Padlet $padlet, Comment $comment) {
            return $padlet->isPublic() || ($user && $user->id === $comment->user_id);
        });

        Gate::define('edit-delete-rating', static function (?User $user, Padlet $padlet, Rating $rating) {
            return $padlet->isPublic() || ($user && $user->id === $rating->user_id);
        });
    }

    protected static function permissionLevelHigherThan(int $permissionLevel, Padlet $padlet, $user) {
        return $padlet->acceptedPadletUsers()
            ->where('user_id', $user->id)
            ->where('permission_level', '>=', $permissionLevel)
            ->exists();
    }

    protected static function hasPermission(int $permissionLevel, Padlet $padlet, ?User $user) {
        if (!$padlet->isPublic() && $user) {
            return self::permissionLevelHigherThan($permissionLevel, $padlet, $user);
        }
        return $padlet->isPublic();
    }
}
