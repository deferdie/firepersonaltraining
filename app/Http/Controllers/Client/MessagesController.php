<?php

namespace App\Http\Controllers\Client;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\MessageRead;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class MessagesController extends Controller
{
    public function markRead(Conversation $conversation): JsonResponse
    {
        $client = Client::where('user_id', auth()->id())->firstOrFail();

        if ($conversation->trainer_id !== $client->trainer_id) {
            abort(403);
        }
        if ($conversation->type === 'client') {
            if ((int) $conversation->target_id !== (int) $client->id) {
                abort(403);
            }
        } elseif ($conversation->type === 'group') {
            if (! $client->groups()->where('group_id', $conversation->target_id)->exists()) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $clientUserId = $client->user_id;
        if (! $clientUserId) {
            return response()->json(['ok' => true]);
        }

        if ($conversation->type === 'client') {
            $unreadIds = $conversation->messages()
                ->where('sender_type', 'trainer')
                ->whereDoesntHave('reads', fn ($r) => $r->where('reader_id', $clientUserId))
                ->pluck('id');
        } else {
            $unreadIds = $conversation->messages()
                ->where(function ($q) use ($client) {
                    $q->where('sender_type', 'trainer')
                        ->orWhere(function ($q2) use ($client) {
                            $q2->where('sender_type', 'client')->where('sender_id', '!=', $client->id);
                        });
                })
                ->whereDoesntHave('reads', fn ($r) => $r->where('reader_id', $clientUserId))
                ->pluck('id');
        }

        $now = now();
        foreach ($unreadIds as $msgId) {
            MessageRead::firstOrCreate(
                [
                    'conversation_message_id' => $msgId,
                    'reader_id' => $clientUserId,
                ],
                ['read_at' => $now]
            );
        }

        return response()->json(['ok' => true]);
    }

    public function store(Request $request): RedirectResponse
    {
        $client = Client::where('user_id', auth()->id())->firstOrFail();

        $validated = $request->validate([
            'conversation_id' => 'required|integer|exists:conversations,id',
            'body' => 'nullable|string|max:10000',
            'payload_type' => 'nullable|string|in:text,workout,image,file,schedule,audio',
            'payload' => 'nullable|array',
        ]);

        $conversation = Conversation::findOrFail($validated['conversation_id']);

        if ($conversation->trainer_id !== $client->trainer_id) {
            abort(403);
        }
        if ($conversation->type === 'client') {
            if ((int) $conversation->target_id !== (int) $client->id) {
                abort(403);
            }
        } elseif ($conversation->type === 'group') {
            if (! $client->groups()->where('group_id', $conversation->target_id)->exists()) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $payloadType = $validated['payload_type'] ?? 'text';
        $payload = $validated['payload'] ?? null;
        $body = $validated['body'] ?? null;
        if ($payloadType === 'text' && $body === null) {
            $body = '';
        }

        $message = $conversation->messages()->create([
            'sender_type' => 'client',
            'sender_id' => $client->id,
            'body' => $body,
            'payload_type' => $payloadType,
            'payload' => $payload,
        ]);

        $conversation->update(['last_message_at' => $message->created_at]);

        event(new MessageSent($message));

        return redirect()
            ->route('client.dashboard', ['tab' => 'chat', 'conversation' => $conversation->id])
            ->with('success', 'Message sent.');
    }
}
