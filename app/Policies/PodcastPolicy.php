<?php

namespace App\Policies;

use App\Models\Podcast;
use App\Models\User;

class PodcastPolicy
{
    // Tous les utilisateurs peuvent voir les podcasts
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Podcast $podcast): bool
    {
        return true;
    }

    // Seuls les animateurs et admins peuvent créer
    public function create(User $user): bool
    {
        return $user->role === 'animateur' || $user->role === 'administrateur';
    }

    // L'animateur propriétaire ou l'admin peuvent modifier
    public function update(User $user, Podcast $podcast): bool
    {
        return $user->role === 'administrateur' || $user->id === $podcast->user_id;
    }

    // L'animateur propriétaire ou l'admin peuvent supprimer
    public function delete(User $user, Podcast $podcast): bool
    {
        return $user->role === 'administrateur' || $user->id === $podcast->user_id;
    }
}