<?php

namespace App\Enums;

enum ActivityType: string
{
    case TRAINER_NOTE = 'trainer_note';
    case PROGRESS_PHOTO = 'progress_photo';
    case WORKOUT_COMPLETION = 'workout_completion';
    case FOOD_ENTRY = 'food_entry';
    case GOAL_ACHIEVEMENT = 'goal_achievement';
    case MESSAGE = 'message';

    public function label(): string
    {
        return match($this) {
            self::TRAINER_NOTE => 'Trainer Note',
            self::PROGRESS_PHOTO => 'Progress Photo',
            self::WORKOUT_COMPLETION => 'Workout Completion',
            self::FOOD_ENTRY => 'Food Entry',
            self::GOAL_ACHIEVEMENT => 'Goal Achievement',
            self::MESSAGE => 'Message',
        };
    }
}
