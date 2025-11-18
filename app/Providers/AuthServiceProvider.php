<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Podcast;
use App\Models\Episode;
use App\Policies\PodcastPolicy;
use App\Policies\EpisodePolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Associe les modèles à leurs policies
     */
    protected $policies = [
        Podcast::class => PodcastPolicy::class,
        Episode::class => EpisodePolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Enregistre les policies
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}