<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = User::where('is_trainer', true)->get();

        foreach ($trainers as $trainer) {
            $clients = Client::where('trainer_id', $trainer->id)->limit(3)->get();
            foreach ($clients as $client) {
                $conv = Conversation::firstOrCreate(
                    [
                        'trainer_id' => $trainer->id,
                        'type' => 'client',
                        'target_id' => $client->id,
                    ],
                    ['last_message_at' => now()]
                );
                $this->seedMessages($conv, $trainer, $client);
            }

            $groups = Group::where('trainer_id', $trainer->id)->limit(2)->get();
            foreach ($groups as $group) {
                $conv = Conversation::firstOrCreate(
                    [
                        'trainer_id' => $trainer->id,
                        'type' => 'group',
                        'target_id' => $group->id,
                    ],
                    ['last_message_at' => now()]
                );
                $this->seedMessages($conv, $trainer, null, $group);
            }
        }
    }

    private function seedMessages(Conversation $conv, User $trainer, ?Client $client = null, ?Group $group = null): void
    {
        if ($conv->messages()->exists()) {
            return;
        }

        $name = $client?->name ?? $group?->name ?? 'Someone';

        ConversationMessage::create([
            'conversation_id' => $conv->id,
            'sender_type' => 'trainer',
            'sender_id' => $trainer->id,
            'body' => "Hey {$name}! Looking forward to working with you.",
            'payload_type' => 'text',
        ]);

        if ($client && $client->user_id) {
            ConversationMessage::create([
                'conversation_id' => $conv->id,
                'sender_type' => 'client',
                'sender_id' => $client->id,
                'body' => 'Thanks! Excited to get started.',
                'payload_type' => 'text',
            ]);
        }

        ConversationMessage::create([
            'conversation_id' => $conv->id,
            'sender_type' => 'trainer',
            'sender_id' => $trainer->id,
            'body' => "Let me know if you have any questions about your program.",
            'payload_type' => 'text',
        ]);

        $conv->update(['last_message_at' => now()]);
    }
}
