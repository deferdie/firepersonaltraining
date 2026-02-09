<?php

namespace App\Models;

use App\Models\Concerns\CreatesActivityFeed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodEntry extends Model
{
    use HasFactory, CreatesActivityFeed;

    protected $fillable = [
        'client_id',
        'meal_type',
        'food_name',
        'calories',
        'protein',
        'carbs',
        'fat',
        'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'calories' => 'integer',
            'protein' => 'decimal:2',
            'carbs' => 'decimal:2',
            'fat' => 'decimal:2',
            'logged_at' => 'datetime',
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
            'meal_type' => $this->meal_type,
            'food_name' => $this->food_name,
            'calories' => $this->calories,
        ];
    }

    /**
     * Food entries are performed by the client (user)
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
