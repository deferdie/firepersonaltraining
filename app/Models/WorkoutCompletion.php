<?php

namespace App\Models;

use App\Models\Concerns\CreatesActivityFeed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutCompletion extends Model
{
    use HasFactory, CreatesActivityFeed;

    protected $fillable = [
        'client_id',
        'workout_id',
        'completed_at',
        'duration_minutes',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }

    /**
     * Get metadata for activity feed
     */
    public function getActivityMetadata(): array
    {
        // Ensure workout and exercises are loaded
        if (!$this->relationLoaded('workout')) {
            $this->load('workout.exercises');
        }

        $exercises = [];
        if ($this->workout && $this->workout->relationLoaded('exercises')) {
            foreach ($this->workout->exercises as $exercise) {
                $exercises[] = [
                    'name' => $exercise->name,
                    'sets' => $exercise->sets ? $exercise->sets . ' sets' : null,
                    'reps' => $exercise->reps,
                ];
            }
        }

        return [
            'workout_name' => $this->workout?->name ?? 'Workout',
            'duration_minutes' => $this->duration_minutes,
            'exercises' => $exercises,
            'notes' => $this->notes,
        ];
    }

    /**
     * Workout completions are performed by the client (user)
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
