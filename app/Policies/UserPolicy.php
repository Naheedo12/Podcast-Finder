<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'administrateur';
    }

    public function view(User $user, User $model): bool
    {
        return $user->role === 'administrateur' || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'administrateur';
    }

    public function update(User $user, User $model): bool
    {
        return $user->role === 'administrateur' || $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->role === 'administrateur' && $user->id !== $model->id;
    }

    public function changeRole(User $user): bool
    {
        return $user->role === 'administrateur';
    }
    public function hello(User $user)
    {
        return $user->role === 'animateur';
    }
}