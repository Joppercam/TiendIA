<?php

namespace App\Policies;

use App\Models\Answer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnswerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Answer $answer)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Answer $answer)
    {
        return $user->id === $answer->user_id || $user->hasRole(['admin', 'editor', 'seller']);
    }

    public function delete(User $user, Answer $answer)
    {
        return $user->id === $answer->user_id || $user->hasRole(['admin', 'editor']);
    }

    public function approve(User $user, Answer $answer)
    {
        return $user->hasRole(['admin', 'editor']);
    }

    public function markAsSeller(User $user, Answer $answer)
    {
        return $user->hasRole(['admin', 'seller']);
    }
}