<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // Seul l'admin peut voir la liste des utilisateurs
    public function viewAny(User $user): bool
    {
        return $user->role === 'administrateur';
    }

    // Un utilisateur peut voir son profil, l'admin peut voir tous les profils
    public function view(User $user, User $model): bool
    {
        return $user->role === 'administrateur' || $user->id === $model->id;
    }

    // Seul l'admin peut créer des utilisateurs
    public function create(User $user): bool
    {
        return $user->role === 'administrateur';
    }

    // Un utilisateur peut modifier son profil, l'admin peut modifier tous les profils
    public function update(User $user, User $model): bool
    {
        return $user->role === 'administrateur' || $user->id === $model->id;
    }

    // Seul l'admin peut supprimer (mais pas lui-même)
    public function delete(User $user, User $model): bool
    {
        return $user->role === 'administrateur' && $user->id !== $model->id;
    }

    // Seul l'admin peut changer les rôles
    public function changeRole(User $user): bool
    {
        return $user->role === 'administrateur';
    }
}