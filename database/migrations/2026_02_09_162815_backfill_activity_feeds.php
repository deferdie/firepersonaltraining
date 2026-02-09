<?php

use App\Enums\ActivityType;
use App\Models\ActivityFeed;
use App\Models\FoodEntry;
use App\Models\ProgressPhoto;
use App\Models\TrainerNote;
use App\Models\WorkoutCompletion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill Trainer Notes
        TrainerNote::chunk(100, function ($notes) {
            foreach ($notes as $note) {
                ActivityFeed::create([
                    'client_id' => $note->client_id,
                    'activity_type' => ActivityType::TRAINER_NOTE->value,
                    'activity_id' => $note->id,
                    'performed_by_type' => 'App\Models\User',
                    'performed_by_id' => $note->trainer_id,
                    'metadata' => [
                        'category' => $note->category ?? 'General',
                        'content_preview' => mb_substr($note->content, 0, 200),
                    ],
                    'created_at' => $note->created_at,
                    'updated_at' => $note->updated_at,
                ]);
            }
        });

        // Backfill Progress Photos
        ProgressPhoto::chunk(100, function ($photos) {
            foreach ($photos as $photo) {
                $performer = $photo->client->user ?? $photo->client->trainer;
                
                ActivityFeed::create([
                    'client_id' => $photo->client_id,
                    'activity_type' => ActivityType::PROGRESS_PHOTO->value,
                    'activity_id' => $photo->id,
                    'performed_by_type' => get_class($performer),
                    'performed_by_id' => $performer->id,
                    'metadata' => [
                        'photo_url' => $photo->photo_url,
                        'angle' => $photo->angle,
                        'weight' => $photo->weight,
                    ],
                    'created_at' => $photo->taken_at ?? $photo->created_at,
                    'updated_at' => $photo->updated_at,
                ]);
            }
        });

        // Backfill Workout Completions
        WorkoutCompletion::with(['workout.exercises'])->chunk(100, function ($completions) {
            foreach ($completions as $completion) {
                $performer = $completion->client->user ?? $completion->client->trainer;
                
                $exercises = [];
                if ($completion->workout) {
                    foreach ($completion->workout->exercises as $exercise) {
                        $exercises[] = [
                            'name' => $exercise->name,
                            'sets' => $exercise->sets ? $exercise->sets . ' sets' : null,
                            'reps' => $exercise->reps,
                        ];
                    }
                }

                ActivityFeed::create([
                    'client_id' => $completion->client_id,
                    'activity_type' => ActivityType::WORKOUT_COMPLETION->value,
                    'activity_id' => $completion->id,
                    'performed_by_type' => get_class($performer),
                    'performed_by_id' => $performer->id,
                    'metadata' => [
                        'workout_name' => $completion->workout?->name ?? 'Workout',
                        'duration_minutes' => $completion->duration_minutes,
                        'exercises' => $exercises,
                        'notes' => $completion->notes,
                    ],
                    'created_at' => $completion->completed_at ?? $completion->created_at,
                    'updated_at' => $completion->updated_at,
                ]);
            }
        });

        // Backfill Food Entries
        FoodEntry::chunk(100, function ($entries) {
            foreach ($entries as $entry) {
                $performer = $entry->client->user ?? $entry->client->trainer;
                
                ActivityFeed::create([
                    'client_id' => $entry->client_id,
                    'activity_type' => ActivityType::FOOD_ENTRY->value,
                    'activity_id' => $entry->id,
                    'performed_by_type' => get_class($performer),
                    'performed_by_id' => $performer->id,
                    'metadata' => [
                        'meal_type' => $entry->meal_type,
                        'food_name' => $entry->food_name,
                        'calories' => $entry->calories,
                    ],
                    'created_at' => $entry->logged_at ?? $entry->created_at,
                    'updated_at' => $entry->updated_at,
                ]);
            }
        });
    }

    public function down(): void
    {
        // Remove all backfilled activity feeds
        DB::table('activity_feeds')->truncate();
    }
};
