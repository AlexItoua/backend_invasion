<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    public function create(User $user, Conversation $conversation)
    {
        return $conversation->participants->contains($user->id);
    }
}
