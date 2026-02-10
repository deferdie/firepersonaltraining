<?php

namespace App\Http\Controllers\Client\Traits;

use App\Models\Client;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use Illuminate\Http\Request;

trait ClientMessagesData
{
    protected function getMessagesData(Client $client, Request $request): array
    {
        $trainerId = $client->trainer_id;
        $clientUserId = $client->user_id;
        $colors = [
            'bg-orange-500', 'bg-blue-500', 'bg-green-500', 'bg-purple-500',
            'bg-indigo-500', 'bg-pink-500', 'bg-yellow-500', 'bg-red-500',
        ];

        $conversations = [];
        $totalUnread = 0;

        if ($trainerId) {
            $oneToOne = Conversation::firstOrCreate(
                [
                    'trainer_id' => $trainerId,
                    'type' => 'client',
                    'target_id' => $client->id,
                ],
                ['last_message_at' => now()]
            );
            $unread = $this->getConversationUnreadCount($oneToOne, $clientUserId, 'client');
            $totalUnread += $unread;
            $lastMsg = $oneToOne->messages()->orderByDesc('created_at')->first();
            $trainer = $client->trainer;
            $trainerInitials = $trainer ? $this->initialsFromName($trainer->name) : 'T';
            $conversations[] = [
                'id' => $oneToOne->id,
                'type' => 'client',
                'name' => $trainer?->name ?? 'Trainer',
                'initials' => $trainerInitials,
                'color' => 'bg-gradient-to-br from-gray-700 to-gray-900',
                'lastMessage' => $lastMsg ? $this->messagePreview($lastMsg) : null,
                'unreadCount' => $unread,
            ];
        }

        $client->load('groups');
        foreach ($client->groups as $group) {
            $conv = Conversation::firstOrCreate(
                [
                    'trainer_id' => $group->trainer_id,
                    'type' => 'group',
                    'target_id' => $group->id,
                ],
                ['last_message_at' => now()]
            );
            $unread = $this->getConversationUnreadCount($conv, $clientUserId, 'group');
            $totalUnread += $unread;
            $lastMsg = $conv->messages()->orderByDesc('created_at')->first();
            $initials = $this->initialsFromName($group->name);
            $color = $colors[abs(crc32($group->name)) % count($colors)];
            $conversations[] = [
                'id' => $conv->id,
                'type' => 'group',
                'name' => $group->name,
                'initials' => $initials,
                'color' => $color,
                'lastMessage' => $lastMsg ? $this->messagePreview($lastMsg) : null,
                'unreadCount' => $unread,
            ];
        }

        $requestedId = $request->has('conversation') ? (int) $request->get('conversation') : null;
        $selected = null;
        foreach ($conversations as $c) {
            if ((int) $c['id'] === $requestedId) {
                $selected = $c;
                break;
            }
        }
        if (! $selected && count($conversations) > 0) {
            $selected = $conversations[0];
        }
        $selectedConversationId = $selected ? (int) $selected['id'] : null;

        $messages = [];
        if ($selectedConversationId) {
            $conv = Conversation::find($selectedConversationId);
            if ($conv && $this->clientCanAccessConversation($client, $conv)) {
                $messages = $conv->messages()
                    ->orderBy('created_at', 'asc')
                    ->limit(100)
                    ->get()
                    ->map(fn (ConversationMessage $m) => $this->formatMessageForClient($m, $clientUserId, $conv->type))
                    ->toArray();
            }
        }

        return [
            'conversations' => $conversations,
            'messages' => $messages,
            'unreadCount' => $totalUnread,
            'conversationId' => $selectedConversationId,
            'selectedConversationId' => $selectedConversationId,
        ];
    }

    protected function clientCanAccessConversation(Client $client, Conversation $conv): bool
    {
        if ($conv->trainer_id !== $client->trainer_id) {
            return false;
        }
        if ($conv->type === 'client') {
            return (int) $conv->target_id === (int) $client->id;
        }
        if ($conv->type === 'group') {
            return $client->groups()->where('group_id', $conv->target_id)->exists();
        }
        return false;
    }

    protected function getConversationUnreadCount(Conversation $conv, ?int $clientUserId, string $convType): int
    {
        if (! $clientUserId) {
            return 0;
        }
        if ($convType === 'client') {
            return $conv->messages()
                ->where('sender_type', 'trainer')
                ->whereDoesntHave('reads', fn ($r) => $r->where('reader_id', $clientUserId))
                ->count();
        }
        $client = Client::where('user_id', $clientUserId)->first();
        if (! $client) {
            return 0;
        }
        return $conv->messages()
            ->where(function ($q) use ($client) {
                $q->where('sender_type', 'trainer')
                    ->orWhere(function ($q2) use ($client) {
                        $q2->where('sender_type', 'client')->where('sender_id', '!=', $client->id);
                    });
            })
            ->whereDoesntHave('reads', fn ($r) => $r->where('reader_id', $clientUserId))
            ->count();
    }

    protected function messagePreview(ConversationMessage $m): string
    {
        if ($m->payload_type === 'text' && $m->body) {
            return \Str::limit($m->body, 50);
        }
        return match ($m->payload_type) {
            'workout' => 'Workout update',
            'image' => 'Image',
            'file' => 'File',
            'schedule' => 'Schedule',
            'audio' => 'Audio message',
            default => 'Message',
        };
    }

    protected function formatMessageForClient(ConversationMessage $m, ?int $clientUserId, string $convType): array
    {
        $sender = $m->sender_type === 'trainer' ? 'trainer' : 'client';
        $read = false;
        if ($clientUserId) {
            $read = $m->reads()->where('reader_id', $clientUserId)->exists();
        }
        return [
            'id' => $m->id,
            'sender' => $sender,
            'type' => $m->payload_type ?? 'text',
            'message' => $m->body,
            'content' => $m->payload,
            'timestamp' => $m->created_at->format('g:i A'),
            'read' => $read,
        ];
    }
}
