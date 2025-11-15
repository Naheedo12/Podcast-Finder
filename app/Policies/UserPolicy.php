<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'administrateur';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Un utilisateur peut voir son propre profil, les admins peuvent voir tous les profils
        return $user->id === $model->id || $user->role === 'administrateur';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'administrateur';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Un utilisateur peut modifier son propre profil, les admins peuvent modifier tous les profils
        return $user->id === $model->id || $user->role === 'administrateur';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Un admin peut supprimer n'importe quel utilisateur sauf lui-même
        return $user->role === 'administrateur' && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->role === 'administrateur';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->role === 'administrateur' && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can change user roles.
     */
    public function changeRole(User $user): bool
    {
        return $user->role === 'administrateur';
    }
}