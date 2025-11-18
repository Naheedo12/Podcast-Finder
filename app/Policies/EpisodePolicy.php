<?php

namespace App\Policies;

use App\Models\Episode;
use App\Models\User;

class EpisodePolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Episode $episode): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'animateur' || $user->role === 'administrateur';
    }

    public function update(User $user, Episode $episode): bool
    {
        if (!$episode->podcast) {
            return false;
        }
        return $user->role === 'administrateur' || $user->id === $episode->podcast->user_id;
    }

    public function delete(User $user, Episode $episode): bool
    {
        if (!$episode->podcast) {
            return false;
        }
        return $user->role === 'administrateur' || $user->id === $episode->podcast->user_id;
    }
}