<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->trainer_id === $user->id;
    }

    public function update(User $user, Conversation $conversation): bool
    {
        return $conversation->trainer_id === $user->id;
    }
}
