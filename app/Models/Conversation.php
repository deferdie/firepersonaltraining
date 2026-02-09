<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'trainer_id',
        'type',
        'target_id',
        'pinned',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'pinned' => 'boolean',
            'last_message_at' => 'datetime',
        ];
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ConversationMessage::class, 'conversation_id');
    }

    public function reads(): HasMany
    {
        return $this->hasManyThrough(MessageRead::class, ConversationMessage::class, 'conversation_id', 'conversation_message_id');
    }

    public function targetClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'target_id');
    }

    public function targetGroup(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'target_id');
    }

    public function scopeForTrainer($query, int $trainerId)
    {
        return $query->where('trainer_id', $trainerId);
    }

    public function scopeClient($query)
    {
        return $query->where('type', 'client');
    }

    public function scopeGroup($query)
    {
        return $query->where('type', 'group');
    }

    public function scopeOrdered($query)
    {
        return $query->orderByRaw('pinned DESC')
            ->orderByDesc('last_message_at');
    }
}
