<?php

namespace App\Http\Controllers\Trainer;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\Group;
use App\Models\MessageRead;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MessagesController extends Controller
{
    public function index(Request $request): Response
    {
        $trainerId = auth()->id();
        $search = $request->get('search', '');
        $conversationsData = $this->buildConversationsList($trainerId, $search);
        $openGroupId = $request->has('group') ? (int) $request->get('group') : null;

        return Inertia::render('Trainer/Messages/Index', [
            'conversations' => $conversationsData,
            'filters' => ['search' => $search],
            'openGroupId' => $openGroupId,
        ]);
    }

    private function buildConversationsList(int $trainerId, string $search): array
    {
        $query = Conversation::forTrainer($trainerId)
            ->with(['targetClient', 'targetGroup'])
            ->ordered();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('targetClient', fn ($c) =>
                    $c->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")
                )->orWhereHas('targetGroup', fn ($c) =>
                    $c->where('name', 'like', "%{$search}%")
                );
            });
        }

        $conversations = $query->get();
        $existingTargets = $conversations->map(fn ($c) => $c->type . ':' . $c->target_id)->flip();
        $colors = [
            'bg-orange-500', 'bg-blue-500', 'bg-green-500', 'bg-purple-500',
            'bg-indigo-500', 'bg-pink-500', 'bg-yellow-500', 'bg-red-500',
        ];

        $items = $conversations->map(function (Conversation $conv) use ($colors, $trainerId) {
            $name = '';
            $initials = '';
            $color = $colors[0];
            if ($conv->type === 'client' && $conv->targetClient) {
                $c = $conv->targetClient;
                $name = $c->name;
                $nameParts = explode(' ', $c->name);
                foreach ($nameParts as $p) {
                    if (!empty($p)) $initials .= strtoupper(substr($p, 0, 1));
                }
                $initials = substr($initials, 0, 2) ?: '?';
                $color = $colors[abs(crc32($c->name)) % count($colors)];
            } elseif ($conv->type === 'group' && $conv->targetGroup) {
                $g = $conv->targetGroup;
                $name = $g->name;
                $nameParts = explode(' ', $g->name);
                foreach ($nameParts as $p) {
                    if (!empty($p)) $initials .= strtoupper(substr($p, 0, 1));
                }
                $initials = substr($initials, 0, 2) ?: 'G';
                $color = $colors[abs(crc32($g->name)) % count($colors)];
            }

            $lastMessage = $conv->messages()->orderByDesc('created_at')->first();
            $lastMessageText = $lastMessage ? $this->messagePreview($lastMessage) : null;
            $lastMessageAt = $lastMessage?->created_at;

            $unread = $conv->messages()
                ->where('sender_type', 'client')
                ->whereDoesntHave('reads', fn ($r) => $r->where('reader_id', $trainerId))
                ->count();

            return [
                'id' => $conv->id,
                'type' => $conv->type,
                'targetId' => $conv->target_id,
                'name' => $name,
                'initials' => $initials,
                'color' => $color,
                'lastMessage' => $lastMessageText,
                'timestamp' => $lastMessageAt ? $this->formatTimestamp($lastMessageAt) : null,
                'unread' => (int) $unread,
                'pinned' => $conv->pinned,
            ];
        });

        if (!$search) {
            $existingClientIds = $conversations->where('type', 'client')->pluck('target_id')->toArray();
            $clientsWithoutConv = Client::where('trainer_id', $trainerId)
                ->whereNotIn('id', $existingClientIds)
                ->get();
            foreach ($clientsWithoutConv as $c) {
                $items = $items->push([
                    'id' => null,
                    'type' => 'client',
                    'targetId' => $c->id,
                    'name' => $c->name,
                    'initials' => substr(collect(explode(' ', $c->name))->map(fn ($p) => substr($p, 0, 1))->join(''), 0, 2) ?: '?',
                    'color' => $colors[abs(crc32($c->name)) % count($colors)],
                    'lastMessage' => null,
                    'timestamp' => null,
                    'unread' => 0,
                    'pinned' => false,
                ]);
            }
            $existingGroupIds = $conversations->where('type', 'group')->pluck('target_id')->toArray();
            $groupsWithoutConv = Group::where('trainer_id', $trainerId)
                ->whereNotIn('id', $existingGroupIds)
                ->get();
            foreach ($groupsWithoutConv as $g) {
                $items = $items->push([
                    'id' => null,
                    'type' => 'group',
                    'targetId' => $g->id,
                    'name' => $g->name,
                    'initials' => substr(collect(explode(' ', $g->name))->map(fn ($p) => substr($p, 0, 1))->join(''), 0, 2) ?: 'G',
                    'color' => $colors[abs(crc32($g->name)) % count($colors)],
                    'lastMessage' => null,
                    'timestamp' => null,
                    'unread' => 0,
                    'pinned' => false,
                ]);
            }
        }

        return $items->values()->toArray();
    }

    public function show(Request $request, Conversation $conversation): Response
    {
        if ($conversation->trainer_id !== auth()->id()) {
            abort(403);
        }

        $trainerId = auth()->id();

        // Mark client messages as read when trainer opens the conversation
        $unreadIds = $conversation->messages()
            ->where('sender_type', 'client')
            ->whereDoesntHave('reads', fn ($r) => $r->where('reader_id', $trainerId))
            ->pluck('id');
        $now = now();
        foreach ($unreadIds as $msgId) {
            MessageRead::firstOrCreate(
                ['conversation_message_id' => $msgId, 'reader_id' => $trainerId],
                ['read_at' => $now]
            );
        }
        $search = $request->get('search', '');
        $conversationsData = $this->buildConversationsList($trainerId, $search);

        $colors = [
            'bg-orange-500', 'bg-blue-500', 'bg-green-500', 'bg-purple-500',
            'bg-indigo-500', 'bg-pink-500', 'bg-yellow-500', 'bg-red-500',
        ];

        $name = '';
        $initials = '';
        $color = $colors[0];
        if ($conversation->type === 'client' && $conversation->targetClient) {
            $c = $conversation->targetClient;
            $name = $c->name;
            $nameParts = explode(' ', $c->name);
            foreach ($nameParts as $p) {
                if (!empty($p)) $initials .= strtoupper(substr($p, 0, 1));
            }
            $initials = substr($initials, 0, 2) ?: '?';
            $color = $colors[abs(crc32($c->name)) % count($colors)];
        } elseif ($conversation->type === 'group' && $conversation->targetGroup) {
            $g = $conversation->targetGroup;
            $name = $g->name;
            $nameParts = explode(' ', $g->name);
            foreach ($nameParts as $p) {
                if (!empty($p)) $initials .= strtoupper(substr($p, 0, 1));
            }
            $initials = substr($initials, 0, 2) ?: 'G';
            $color = $colors[abs(crc32($g->name)) % count($colors)];
        }

        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn (ConversationMessage $m) => $this->formatMessage($m, $trainerId))
            ->toArray();

        return Inertia::render('Trainer/Messages/Index', [
            'conversations' => $conversationsData,
            'selectedConversation' => [
                'id' => $conversation->id,
                'type' => $conversation->type,
                'targetId' => $conversation->target_id,
                'name' => $name,
                'initials' => $initials,
                'color' => $color,
            ],
            'messages' => $messages,
            'filters' => ['search' => $search],
        ]);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'conversation_id' => 'nullable|integer|exists:conversations,id',
            'type' => 'required|in:client,group',
            'target_id' => 'required|integer',
            'body' => 'nullable|string|max:10000',
            'payload_type' => 'nullable|string|in:text,workout,image,file,schedule,audio',
            'payload' => 'nullable|array',
        ]);

        $trainerId = auth()->id();

        if (!empty($validated['conversation_id'])) {
            $conversation = Conversation::findOrFail($validated['conversation_id']);
            if ($conversation->trainer_id !== $trainerId) {
                abort(403);
            }
        } else {
            $conversation = Conversation::firstOrCreate(
                [
                    'trainer_id' => $trainerId,
                    'type' => $validated['type'],
                    'target_id' => $validated['target_id'],
                ],
                ['last_message_at' => now()]
            );
        }

        $this->ensureTargetBelongsToTrainer($validated['type'], $validated['target_id'], $trainerId);

        $payloadType = $validated['payload_type'] ?? 'text';
        $payload = $validated['payload'] ?? null;
        $body = $validated['body'] ?? null;
        if ($payloadType === 'text' && $body === null) {
            $body = '';
        }

        $message = $conversation->messages()->create([
            'sender_type' => 'trainer',
            'sender_id' => $trainerId,
            'body' => $body,
            'payload_type' => $payloadType,
            'payload' => $payload,
        ]);

        $conversation->update(['last_message_at' => $message->created_at]);

        event(new MessageSent($message));

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $this->formatMessage($message, $trainerId),
                'conversation_id' => $conversation->id,
            ], 201);
        }

        return redirect()
            ->route('trainer.messages.show', $conversation)
            ->with('success', 'Message sent.');
    }

    public function update(Request $request, Conversation $conversation): \Illuminate\Http\RedirectResponse
    {
        if ($conversation->trainer_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'pinned' => 'sometimes|boolean',
        ]);

        if (isset($validated['pinned'])) {
            $conversation->update(['pinned' => $validated['pinned']]);
        }

        return redirect()->back();
    }

    private function ensureTargetBelongsToTrainer(string $type, int $targetId, int $trainerId): void
    {
        if ($type === 'client') {
            $exists = Client::where('id', $targetId)->where('trainer_id', $trainerId)->exists();
            if (!$exists) abort(403);
        } else {
            $exists = Group::where('id', $targetId)->where('trainer_id', $trainerId)->exists();
            if (!$exists) abort(403);
        }
    }

    private function messagePreview(ConversationMessage $m): string
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

    private function formatTimestamp(\Carbon\Carbon $at): string
    {
        if ($at->isToday()) {
            return $at->format('g:i A');
        }
        if ($at->isYesterday()) {
            return 'Yesterday';
        }
        if ($at->isCurrentYear()) {
            return $at->format('M j');
        }
        return $at->format('M j, Y');
    }

    private function formatMessage(ConversationMessage $m, int $trainerId): array
    {
        $sender = $m->sender_type === 'trainer' ? 'trainer' : 'client';
        $read = false;
        if ($m->sender_type === 'trainer') {
            $read = $m->reads()->where('reader_id', '!=', $trainerId)->exists();
        }

        return [
            'id' => $m->id,
            'sender' => $sender,
            'type' => $m->payload_type,
            'message' => $m->body,
            'content' => $m->payload,
            'timestamp' => $m->created_at->format('g:i A'),
            'read' => $read,
        ];
    }
}
