<?php

namespace App\Events;

use App\Models\ConversationMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ConversationMessage $message
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->message->conversation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $trainerId = $this->message->conversation->trainer_id;

        return [
            'message' => [
                'id' => $this->message->id,
                'sender' => $this->message->sender_type === 'trainer' ? 'trainer' : 'client',
                'type' => $this->message->payload_type,
                'message' => $this->message->body,
                'content' => $this->message->payload,
                'timestamp' => $this->message->created_at->format('g:i A'),
                'read' => false,
            ],
            'conversation_id' => $this->message->conversation_id,
        ];
    }
}
