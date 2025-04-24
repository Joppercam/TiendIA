<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Review $review)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Review $review)
    {
        return $user->id === $review->user_id || $user->hasRole(['admin', 'editor']);
    }

    public function delete(User $user, Review $review)
    {
        return $user->id === $review->user_id || $user->hasRole(['admin', 'editor']);
    }

    public function approve(User $user, Review $review)
    {
        return $user->hasRole(['admin', 'editor']);
    }

    public function feature(User $user, Review $review)
    {
        return $user->hasRole(['admin', 'editor']);
    }
}