<?php

namespace App\Policies;

use App\Models\Podcast;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PodcastPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tous les utilisateurs authentifiés peuvent voir les podcasts
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Podcast $podcast): bool
    {
        return true; // Tous les utilisateurs authentifiés peuvent voir un podcast
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
    public function update(User $user, Podcast $podcast): bool
    {
        return $user->id === $podcast->user_id || $user->role === 'administrateur';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Podcast $podcast): bool
    {
        return $user->id === $podcast->user_id || $user->role === 'administrateur';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Podcast $podcast): bool
    {
        return $user->role === 'administrateur';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Podcast $podcast): bool
    {
        return $user->role === 'administrateur';
    }
}