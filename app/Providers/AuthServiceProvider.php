<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Padlet;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
            if (!$padlet->isPublic() && $user) {
                return $padlet->isOwner($user) || $padlet->acceptedPadletUsers()
                        ->where('user_id', $user->id)
                        ->where('permission_level', '>=', Padlet::PERMISSION_LEVELS['view'])
                        ->exists();
            }
            return $padlet->isPublic();
        });

        Gate::define('comment', static function (?User $user, Padlet $padlet) {
            if (!$padlet->isPublic() && $user) {
                return $padlet->isOwner($user) || $padlet->acceptedPadletUsers()
                        ->where('user_id', $user->id)
                        ->where('permission_level', '>=', Padlet::PERMISSION_LEVELS['comment'])
                        ->exists();
            }
            return $padlet->isPublic();
        });

        Gate::define('edit', static function (?User $user, Padlet $padlet) {
            if (!$padlet->isPublic() && $user) {
                return $padlet->isOwner($user) || $padlet->acceptedPadletUsers()
                        ->where('user_id', $user->id)
                        ->where('permission_level', '>=', Padlet::PERMISSION_LEVELS['edit'])
                        ->exists();
            }
            return $padlet->isPublic();
        });

        Gate::define('admin', static function (?User $user, Padlet $padlet) {
            if (!$padlet->isPublic() && $user) {
                return $padlet->isOwner($user) || $padlet->acceptedPadletUsers()
                        ->where('user_id', $user->id)
                        ->where('permission_level', '>=', Padlet::PERMISSION_LEVELS['admin'])
                        ->exists();
            }
            return $padlet->isPublic();
        });

        // Gates that defines that a user can only delete his own comments & ratings
        Gate::define('edit-delete-comment', static function (?User $user, Padlet $padlet, Comment $comment) {
            return $padlet->isPublic() || ($user && $user->id === $comment->user_id);
        });

        Gate::define('edit-delete-rating', static function (?User $user, Padlet $padlet, Rating $rating) {
            return $padlet->isPublic() || ($user && $user->id === $rating->user_id);
        });
    }
}
