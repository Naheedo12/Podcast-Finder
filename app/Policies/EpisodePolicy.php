<?php

namespace App\Policies;

use App\Models\Episode;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EpisodePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tous les utilisateurs authentifiés peuvent voir les épisodes
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Episode $episode): bool
    {
        return true; // Tous les utilisateurs authentifiés peuvent voir un épisode
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['administrateur', 'animateur']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Episode $episode): bool
    {
        // Vérifiez d'abord si le podcast existe
        if (!$episode->podcast) {
            return false;
        }
        return $user->id === $episode->podcast->user_id || $user->role === 'administrateur';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Episode $episode): bool
    {
        // Vérifiez d'abord si le podcast existe
        if (!$episode->podcast) {
            return false;
        }
        return $user->id === $episode->podcast->user_id || $user->role === 'administrateur';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Episode $episode): bool
    {
        return $user->role === 'administrateur';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Episode $episode): bool
    {
        return $user->role === 'administrateur';
    }
}