<?php

namespace App\Http\Controllers\Client\Traits;

use App\Models\Client;
use Carbon\Carbon;

trait ClientDataTransformer
{
    protected function transformClient(Client $client): array
    {
        $nameParts = explode(' ', $client->name);
        $initials = '';
        foreach ($nameParts as $part) {
            if (! empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        if (strlen($initials) > 2) {
            $initials = substr($initials, 0, 2);
        }

        $colors = [
            'bg-orange-500',
            'bg-blue-500',
            'bg-green-500',
            'bg-purple-500',
            'bg-indigo-500',
            'bg-pink-500',
            'bg-yellow-500',
            'bg-red-500',
        ];
        $colorIndex = abs(crc32($client->name)) % count($colors);
        $color = $colors[$colorIndex];

        $trainer = $client->trainer;
        $trainerInitials = '';
        if ($trainer) {
            $parts = explode(' ', $trainer->name);
            foreach ($parts as $p) {
                if (! empty($p)) {
                    $trainerInitials .= strtoupper(substr($p, 0, 1));
                }
            }
            $trainerInitials = substr($trainerInitials, 0, 2);
        }

        return [
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'initials' => $initials,
            'color' => $color,
            'memberSince' => $client->created_at->format('F Y'),
            'trainer' => $trainer ? [
                'id' => $trainer->id,
                'name' => $trainer->name,
                'initials' => $trainerInitials,
            ] : null,
        ];
    }

    protected function buildStats(Client $client): array
    {
        $workoutCompletions = $client->workoutCompletions;

        $workoutsCompleted = $workoutCompletions->count();

        $streak = 0;
        if ($workoutsCompleted > 0) {
            $completionDates = $workoutCompletions->pluck('completed_at')
                ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
                ->unique()
                ->sortDesc()
                ->values();

            $checkDate = now()->format('Y-m-d');
            foreach ($completionDates as $date) {
                if ($date === $checkDate || $date === Carbon::parse($checkDate)->subDay()->format('Y-m-d')) {
                    $streak++;
                    $checkDate = Carbon::parse($date)->subDay()->format('Y-m-d');
                } else {
                    break;
                }
            }
        }

        $activeProgram = $client->programs()->where('status', 'active')->with('program')->first();
        $workoutsTotal = $activeProgram ? 32 : max($workoutsCompleted, 1);
        $goalPercent = $workoutsTotal > 0 ? (int) round(($workoutsCompleted / $workoutsTotal) * 100, 0) : 0;

        $totalMinutes = $workoutCompletions->sum('duration_minutes') ?? 0;
        $trainingHours = round($totalMinutes / 60, 1);

        $weekStart = now()->startOfWeek();
        $weekWorkouts = $workoutCompletions->filter(
            fn ($c) => Carbon::parse($c->completed_at)->gte($weekStart)
        )->count();

        $weekMinutes = $workoutCompletions->filter(
            fn ($c) => Carbon::parse($c->completed_at)->gte($weekStart)
        )->sum('duration_minutes');
        $weekTrainingHours = round($weekMinutes / 60, 1);

        return [
            'streak' => $streak,
            'workoutsCount' => $workoutsCompleted,
            'goalPercent' => min(100, $goalPercent),
            'trainingHours' => $trainingHours,
            'weekWorkouts' => $weekWorkouts,
            'weekTrainingHours' => $weekTrainingHours,
            'avgHeartRate' => '-',
            'caloriesBurned' => '-',
            'goals' => [],
        ];
    }

    protected function buildLayoutStats(array $stats): array
    {
        return [
            'streak' => $stats['streak'] ?? 0,
            'workoutsCount' => $stats['workoutsCount'] ?? 0,
            'goalPercent' => $stats['goalPercent'] ?? 0,
        ];
    }

    protected function getTodaysWorkout(Client $client): ?array
    {
        $activeProgram = $client->programs()->where('status', 'active')->with('program')->first();
        if (! $activeProgram) {
            return [
                'id' => '1',
                'name' => 'Upper Body Strength',
                'description' => 'Focus on compound movements',
                'duration' => '45 min',
                'exercises' => 8,
                'scheduledTime' => '10:00 AM',
            ];
        }

        $workout = $activeProgram->program->workouts()->first();
        if (! $workout) {
            return [
                'id' => (string) $activeProgram->id,
                'name' => $activeProgram->program->name,
                'description' => $activeProgram->program->description ?? 'Program workout',
                'duration' => '45 min',
                'exercises' => 8,
                'scheduledTime' => '10:00 AM',
            ];
        }

        $exerciseCount = $workout->exercises()->count();

        return [
            'id' => (string) $workout->id,
            'name' => $workout->name,
            'description' => $workout->description ?? 'Assigned workout',
            'duration' => '45 min',
            'exercises' => $exerciseCount ?: 8,
            'scheduledTime' => '10:00 AM',
        ];
    }

    protected function getUpcomingWorkouts(Client $client): array
    {
        return [
            [
                'id' => '2',
                'name' => 'Lower Body Power',
                'date' => 'Tomorrow',
                'time' => '10:00 AM',
                'type' => 'Strength',
            ],
            [
                'id' => '3',
                'name' => 'Core & Cardio',
                'date' => now()->addDays(2)->format('D, M j'),
                'time' => '9:00 AM',
                'type' => 'Conditioning',
            ],
        ];
    }

    protected function getProgramProgress(Client $client): ?array
    {
        $activeProgram = $client->programs()->where('status', 'active')->with('program')->first();
        if (! $activeProgram || ! $activeProgram->program) {
            return null;
        }

        $program = $activeProgram->program;
        $totalWeeks = $program->weeks ?? 12;
        $startDate = $activeProgram->start_date ? Carbon::parse($activeProgram->start_date) : now();
        $currentWeek = min(
            (int) $startDate->diffInWeeks(now()) + 1,
            $totalWeeks
        );
        $percent = $totalWeeks > 0 ? (int) round(($currentWeek / $totalWeeks) * 100, 0) : 0;

        $completedCount = $client->workoutCompletions->count();
        $workoutsInProgram = $program->workouts()->count();
        $totalWorkouts = $workoutsInProgram > 0 ? $workoutsInProgram * $totalWeeks : 12;
        $trainingHours = round($client->workoutCompletions->sum('duration_minutes') / 60, 1);

        return [
            'name' => $program->name . ' (' . $totalWeeks . '-Week Program)',
            'weekLabel' => 'Week ' . $currentWeek . ' of ' . $totalWeeks,
            'percent' => min(100, $percent),
            'completedWorkouts' => $completedCount,
            'totalWorkouts' => $totalWorkouts,
            'trainingHours' => $trainingHours,
            'weightChange' => null,
        ];
    }

    protected function getRecentAchievements(Client $client, array $stats): array
    {
        $achievements = [];

        if ($stats['streak'] >= 7) {
            $achievements[] = [
                'id' => '1',
                'icon' => '🔥',
                'title' => '7-Day Streak!',
                'description' => 'Completed workouts for 7 days straight',
                'color' => 'from-orange-400 to-red-500',
            ];
        }

        if ($stats['workoutsCount'] >= 10) {
            $achievements[] = [
                'id' => '2',
                'icon' => '💪',
                'title' => 'Strength Milestone',
                'description' => 'Completed 10+ workouts!',
                'color' => 'from-blue-400 to-purple-500',
            ];
        }

        if (empty($achievements)) {
            $achievements[] = [
                'id' => '1',
                'icon' => '🎯',
                'title' => 'Getting Started',
                'description' => 'Complete your first workout to earn your first achievement!',
                'color' => 'from-green-400 to-teal-500',
            ];
        }

        return array_slice($achievements, 0, 3);
    }

    protected function initialsFromName(string $name): string
    {
        $parts = explode(' ', $name);
        $initials = '';
        foreach ($parts as $p) {
            if (! empty($p)) {
                $initials .= strtoupper(substr($p, 0, 1));
            }
        }

        return substr($initials, 0, 2) ?: '?';
    }
}

