<?php

namespace App\Models;

use App\Models\Concerns\CreatesActivityFeed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressPhoto extends Model
{
    use HasFactory, CreatesActivityFeed;

    protected $fillable = [
        'client_id',
        'photo_url',
        'angle',
        'weight',
        'notes',
        'taken_at',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'taken_at' => 'datetime',
        ];
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
            'photo_url' => $this->photo_url,
            'angle' => $this->angle,
            'weight' => $this->weight,
        ];
    }

    /**
     * Progress photos are performed by the client (user)
     */
    public function getActivityPerformer(): Model
    {
        if ($this->client && $this->client->user) {
            return $this->client->user;
        }
        
        // Fallback to client's trainer if no user
        return $this->client->trainer;
    }
}
