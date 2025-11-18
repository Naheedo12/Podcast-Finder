<?php

namespace App\Policies;

use App\Models\Podcast;
use App\Models\User;

class PodcastPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Podcast $podcast): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'animateur' || $user->role === 'administrateur';
    }

    public function update(User $user, Podcast $podcast): bool
    {
        return $user->role === 'administrateur' || $user->id === $podcast->user_id;
    }

    public function delete(User $user, Podcast $podcast): bool
    {
        return $user->role === 'administrateur' || $user->id === $podcast->user_id;
    }
}