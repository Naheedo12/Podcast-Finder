<?php

namespace App\Policies;

use App\Models\Episode;
use App\Models\User;

class EpisodePolicy
{
    // Tous les utilisateurs peuvent voir les épisodes
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Episode $episode): bool
    {
        return true;
    }

    // Seuls les animateurs et admins peuvent créer
    public function create(User $user): bool
    {
        return $user->role === 'animateur' || $user->role === 'administrateur';
    }

    // L'animateur propriétaire du podcast ou l'admin peuvent modifier
    public function update(User $user, Episode $episode): bool
    {
        if (!$episode->podcast) {
            return false;
        }
        return $user->role === 'administrateur' || $user->id === $episode->podcast->user_id;
    }

    // L'animateur propriétaire du podcast ou l'admin peuvent supprimer
    public function delete(User $user, Episode $episode): bool
    {
        if (!$episode->podcast) {
            return false;
        }
        return $user->role === 'administrateur' || $user->id === $episode->podcast->user_id;
    }
}