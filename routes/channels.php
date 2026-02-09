<?php

use App\Models\Client;
use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversationId}', function ($user, int $conversationId) {
    $conversation = Conversation::find($conversationId);
    if (! $conversation) {
        return false;
    }
    // Trainer can listen to their conversations
    if ($conversation->trainer_id === $user->id) {
        return true;
    }
    // Client can listen to their 1:1 conversation with trainer
    $client = Client::where('user_id', $user->id)->first();
    if (! $client) {
        return false;
    }
    if ($conversation->type === 'client' && (int) $conversation->target_id === (int) $client->id) {
        return true;
    }
    // Client can listen to group conversation if they are a member
    if ($conversation->type === 'group') {
        return $client->groups()->where('group_id', $conversation->target_id)->exists();
    }
    return false;
});
