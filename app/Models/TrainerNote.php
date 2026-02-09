<?php

namespace App\Models;

use App\Models\Concerns\CreatesActivityFeed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerNote extends Model
{
    use HasFactory, CreatesActivityFeed;

    protected $fillable = [
        'trainer_id',
        'client_id',
        'category',
        'content',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get metadata for activity feed
     */
    public function getActivityMetadata(): array
    {
        return [
            'category' => $this->category ?? 'General',
            'content_preview' => mb_substr($this->content, 0, 200),
        ];
    }
}
