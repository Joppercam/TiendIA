<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Question $question)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Question $question)
    {
        return $user->id === $question->user_id || $user->hasRole(['admin', 'editor']);
    }

    public function delete(User $user, Question $question)
    {
        return $user->id === $question->user_id || $user->hasRole(['admin', 'editor']);
    }

    public function approve(User $user, Question $question)
    {
        return $user->hasRole(['admin', 'editor']);
    }
}