<?php

namespace App\Models;

use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityFeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'activity_type',
        'activity_id',
        'performed_by_type',
        'performed_by_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'activity_type' => ActivityType::class,
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function performedBy(): MorphTo
    {
        return $this->morphTo('performed_by');
    }

    /**
     * Get the related activity model based on activity_type
     * This is a helper method, not a true polymorphic relationship
     */
    public function getActivityAttribute()
    {
        if (!$this->activity_type || !$this->activity_id) {
            return null;
        }

        $modelClass = match($this->activity_type) {
            ActivityType::TRAINER_NOTE => TrainerNote::class,
            ActivityType::PROGRESS_PHOTO => ProgressPhoto::class,
            ActivityType::WORKOUT_COMPLETION => WorkoutCompletion::class,
            ActivityType::FOOD_ENTRY => FoodEntry::class,
            ActivityType::GOAL_ACHIEVEMENT => null, // Goal model doesn't exist yet
            ActivityType::MESSAGE => Message::class,
            default => null,
        };

        if (!$modelClass) {
            return null;
        }

        return $modelClass::find($this->activity_id);
    }

    /**
     * Check if activity type is workout (for backward compatibility)
     */
    public function isWorkoutType(): bool
    {
        return $this->activity_type === ActivityType::WORKOUT_COMPLETION;
    }

    /**
     * Scope to filter by activity type
     */
    public function scopeOfType($query, ActivityType $type)
    {
        return $query->where('activity_type', $type->value);
    }

    /**
     * Scope to get activities for a specific client
     */
    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Get formatted activity data for frontend
     */
    public function toActivityArray(): array
    {
        $base = [
            'id' => $this->id,
            'type' => $this->activity_type->value,
            'date' => $this->created_at->format('Y-m-d'),
            'time' => $this->created_at->format('g:i A'),
            'performed_by' => $this->performedBy?->name ?? 'Unknown',
        ];

        // Add type-specific data based on activity type
        return match($this->activity_type) {
            ActivityType::TRAINER_NOTE => array_merge($base, [
                'title' => 'Note Added',
                'category' => $this->metadata['category'] ?? 'General',
                'content' => $this->metadata['content_preview'] ?? '',
            ]),
            ActivityType::PROGRESS_PHOTO => array_merge($base, [
                'title' => 'Progress Photo Added',
                'photo_url' => $this->metadata['photo_url'] ?? '',
                'angle' => $this->metadata['angle'] ?? '',
                'weight' => $this->metadata['weight'] ?? null,
            ]),
            ActivityType::WORKOUT_COMPLETION => array_merge($base, [
                'title' => $this->metadata['workout_name'] ?? 'Workout Completed',
                'duration' => $this->metadata['duration_minutes'] ? $this->metadata['duration_minutes'] . ' min' : null,
                'exercises' => $this->metadata['exercises'] ?? [],
                'notes' => $this->metadata['notes'] ?? null,
            ]),
            ActivityType::FOOD_ENTRY => array_merge($base, [
                'title' => 'Food Logged',
                'meal_type' => $this->metadata['meal_type'] ?? '',
                'food_name' => $this->metadata['food_name'] ?? '',
                'calories' => $this->metadata['calories'] ?? null,
            ]),
            ActivityType::GOAL_ACHIEVEMENT => array_merge($base, [
                'title' => 'Goal Achieved',
                'goal_name' => $this->metadata['goal_name'] ?? '',
                'goal_description' => $this->metadata['goal_description'] ?? '',
            ]),
            ActivityType::MESSAGE => array_merge($base, [
                'title' => $this->metadata['title'] ?? 'Message',
                'message' => $this->metadata['content'] ?? '',
            ]),
        };
    }
}
