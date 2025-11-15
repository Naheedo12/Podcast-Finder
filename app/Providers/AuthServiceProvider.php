<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Podcast;
use App\Models\Episode;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Podcast::class => PodcastPolicy::class,
        Episode::class => EpisodePolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gates pour les rôles généraux
        Gate::define('is-admin', function (User $user) {
            return $user->role === 'administrateur';
        });

        Gate::define('is-host', function (User $user) {
            return $user->role === 'animateur';
        });

        Gate::define('is-user', function (User $user) {
            return $user->role === 'utilisateur';
        });

        // Gates pour les actions spécifiques
        Gate::define('manage-podcasts', function (User $user) {
            return in_array($user->role, ['administrateur', 'animateur']);
        });

        Gate::define('manage-episodes', function (User $user) {
            return in_array($user->role, ['administrateur', 'animateur']);
        });

        Gate::define('manage-users', function (User $user) {
            return $user->role === 'administrateur';
        });

        // Gates pour la propriété
        Gate::define('own-podcast', function (User $user, Podcast $podcast) {
            return $user->id === $podcast->user_id || $user->role === 'administrateur';
        });

        Gate::define('own-episode', function (User $user, Episode $episode) {
            return $user->id === $episode->podcast->user_id || $user->role === 'administrateur';
        });

        Gate::define('delete-user', function (User $user, User $targetUser) {
            return $user->role === 'administrateur' && $user->id !== $targetUser->id;
        });
    }
}