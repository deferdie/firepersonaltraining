<?php

namespace App\Services;

use App\Enums\SchedulableType;
use App\Models\Schedule;
use Carbon\Carbon;

class ScheduleEventTransformer
{
    private static array $typeColors = [
        'habit' => '#10b981',
        'program' => '#f43f5e',
        'assessment' => '#f59e0b',
        'content' => '#0ea5e9',
        'goal' => '#8b5cf6',
        'nutrition' => '#14b8a6',
    ];

    public static function toCalendarEvents(iterable $schedules, Carbon $rangeStart, Carbon $rangeEnd): array
    {
        $events = [];
        foreach ($schedules as $schedule) {
            $type = self::schedulableType($schedule);
            $color = self::$typeColors[$type] ?? '#6b7280';
            foreach (self::expandSchedule($schedule, $rangeStart, $rangeEnd) as $occurrence) {
                $events[] = self::eventFromSchedule($schedule, $occurrence, $type, $color);
            }
        }
        usort($events, fn ($a, $b) => strcmp($a['start'], $b['start']));
        return $events;
    }

    public static function toUpcomingSessions(iterable $schedules, int $limit = 10): array
    {
        $rangeStart = now();
        $rangeEnd = now()->addDays(60);
        $events = self::toCalendarEvents($schedules, $rangeStart, $rangeEnd);
        $sessions = [];
        foreach (array_slice($events, 0, $limit) as $ev) {
            $start = Carbon::parse($ev['start']);
            $sessions[] = [
                'id' => $ev['id'],
                'scheduleId' => $ev['scheduleId'],
                'type' => $ev['title'],
                'date' => $start->format('Y-m-d'),
                'time' => $start->format('g:i A'),
                'status' => 'scheduled',
            ];
        }
        return $sessions;
    }

    private static function schedulableType(Schedule $schedule): string
    {
        $enum = SchedulableType::fromModelClass($schedule->schedulable_type);
        return $enum?->value ?? 'habit';
    }

    private static function expandSchedule(Schedule $schedule, Carbon $rangeStart, Carbon $rangeEnd): array
    {
        $rule = $schedule->recurrence_rule ?? [];
        $mode = $rule['mode'] ?? 'one_off';
        $interval = (int) ($rule['interval'] ?? 1);
        $weekdays = $rule['weekdays'] ?? [];
        $endsAt = $schedule->recurrence_ends_at
            ? Carbon::parse($schedule->recurrence_ends_at)->endOfDay()
            : $rangeEnd->copy()->addYear();

        $occurrences = [];

        if ($mode === 'one_off') {
            $start = Carbon::parse($schedule->starts_at);
            if ($start->between($rangeStart, $rangeEnd)) {
                $occurrences[] = $start;
            }
            return $occurrences;
        }

        $cursor = Carbon::parse($schedule->starts_at);
        $maxIterations = 500;
        $count = 0;

        while ($cursor->lte($endsAt) && $cursor->lte($rangeEnd) && $count < $maxIterations) {
            if ($cursor->gte($rangeStart)) {
                $include = match ($mode) {
                    'daily' => true,
                    'weekly' => true,
                    'monthly' => true,
                    'weekdays' => in_array((int) $cursor->format('w'), $weekdays, true),
                    default => false,
                };
                if ($include) {
                    $occurrences[] = $cursor->copy();
                }
            }
            $count++;
            $cursor = match ($mode) {
                'daily' => $cursor->addDays($interval),
                'weekly' => $cursor->addWeeks($interval),
                'monthly' => $cursor->addMonths($interval),
                'weekdays' => $cursor->addDay(),
                default => $cursor->addDay(),
            };
        }

        return $occurrences;
    }

    private static function eventFromSchedule(Schedule $schedule, Carbon $start, string $type, string $color): array
    {
        $end = $schedule->ends_at
            ? Carbon::parse($schedule->ends_at)->setDateFrom($start)
            : $start->copy()->addHour();

        return [
            'id' => $schedule->id . '_' . $start->format('Y-m-d-H-i'),
            'scheduleId' => $schedule->id,
            'title' => $schedule->title,
            'start' => $start->toIso8601String(),
            'end' => $end->toIso8601String(),
            'allDay' => false,
            'type' => $type,
            'color' => $color,
            'notes' => $schedule->notes,
        ];
    }
}
