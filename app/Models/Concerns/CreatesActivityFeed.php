<?php

namespace App\Models\Concerns;

use App\Enums\ActivityType;
use App\Models\ActivityFeed;
use Illuminate\Database\Eloquent\Model;

trait CreatesActivityFeed
{
    /**
     * Boot the trait and set up model event listeners
     */
    public static function bootCreatesActivityFeed(): void
    {
        static::created(function (Model $model) {
            $model->createActivityFeed();
        });
    }

    /**
     * Get the activity type for this model
     * Override this method in the model if needed
     */
    public function getActivityType(): ActivityType
    {
        return match(get_class($this)) {
            \App\Models\TrainerNote::class => ActivityType::TRAINER_NOTE,
            \App\Models\ProgressPhoto::class => ActivityType::PROGRESS_PHOTO,
            \App\Models\WorkoutCompletion::class => ActivityType::WORKOUT_COMPLETION,
            \App\Models\FoodEntry::class => ActivityType::FOOD_ENTRY,
            default => throw new \Exception('Activity type not defined for ' . get_class($this)),
        };
    }

    /**
     * Get the metadata for the activity feed
     * Override this method in the model to customize metadata
     */
    public function getActivityMetadata(): array
    {
        return [];
    }

    /**
     * Get who performed this activity
     * Override this method in the model if needed
     */
    public function getActivityPerformer(): Model
    {
        // Default: try to get trainer_id or user_id
        if (isset($this->trainer_id)) {
            return $this->trainer;
        }
        if (isset($this->user_id)) {
            return $this->user;
        }
        
        // Fallback: try to get client and use their trainer
        if (isset($this->client_id) && $this->client) {
            return $this->client->trainer;
        }

        throw new \Exception('Could not determine activity performer for ' . get_class($this));
    }

    /**
     * Get the client ID for this activity
     * Override this method in the model if needed
     */
    public function getActivityClientId(): int
    {
        if (isset($this->client_id)) {
            return $this->client_id;
        }

        throw new \Exception('Could not determine client_id for ' . get_class($this));
    }

    /**
     * Create an activity feed entry for this model
     */
    public function createActivityFeed(): ActivityFeed
    {
        $performer = $this->getActivityPerformer();

        return ActivityFeed::create([
            'client_id' => $this->getActivityClientId(),
            'activity_type' => $this->getActivityType()->value,
            'activity_id' => $this->id,
            'performed_by_type' => get_class($performer),
            'performed_by_id' => $performer->id,
            'metadata' => $this->getActivityMetadata(),
        ]);
    }
}
