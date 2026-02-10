<?php

namespace App\Enums;

enum SchedulableType: string
{
    case HABIT = 'habit';
    case PROGRAM = 'program';
    case ASSESSMENT = 'assessment';
    case CONTENT = 'content';
    case GOAL = 'goal';
    case NUTRITION = 'nutrition';

    public function label(): string
    {
        return match ($this) {
            self::HABIT => 'Habit',
            self::PROGRAM => 'Program',
            self::ASSESSMENT => 'Assessment',
            self::CONTENT => 'Content',
            self::GOAL => 'Goal',
            self::NUTRITION => 'Nutrition',
        };
    }

    public static function fromModelClass(string $class): ?self
    {
        return match ($class) {
            \App\Models\Habit::class => self::HABIT,
            default => null,
        };
    }

    public function modelClass(): ?string
    {
        return match ($this) {
            self::HABIT => \App\Models\Habit::class,
            default => null,
        };
    }

    public function tableName(): ?string
    {
        return match ($this) {
            self::HABIT => 'habits',
            default => null,
        };
    }
}
