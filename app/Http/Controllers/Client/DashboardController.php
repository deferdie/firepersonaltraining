<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\ConversationMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $client = Client::where('user_id', auth()->id())->firstOrFail();

        $client->load([
            'trainer',
            'programs.program',
            'workoutCompletions.workout',
        ]);

        // Build client data with initials and color
        $clientData = $this->transformClient($client);

        // Build stats
        $stats = $this->buildStats($client);

        // Today's workout (mock until scheduling is built)
        $todayWorkout = $this->getTodaysWorkout($client);

        // Upcoming workouts (mock)
        $upcomingWorkouts = $this->getUpcomingWorkouts($client);

        // Program progress
        $programProgress = $this->getProgramProgress($client);

        // Recent achievements (mock)
        $recentAchievements = $this->getRecentAchievements($client, $stats);

        // Messages: 1:1 with trainer + group conversations
        $messagesData = $this->getMessagesData($client, $request);

        $initialTab = $request->get('tab', 'home');
        if (! in_array($initialTab, ['home', 'workouts', 'progress', 'chat', 'profile'])) {
            $initialTab = 'home';
        }

        return Inertia::render('Client/Dashboard', [
            'client' => $clientData,
            'stats' => $stats,
            'todayWorkout' => $todayWorkout,
            'upcomingWorkouts' => $upcomingWorkouts,
            'programProgress' => $programProgress,
            'recentAchievements' => $recentAchievements,
            'conversations' => $messagesData['conversations'],
            'messages' => $messagesData['messages'],
            'unreadMessagesCount' => $messagesData['unreadCount'],
            'conversationId' => $messagesData['conversationId'],
            'selectedConversationId' => $messagesData['selectedConversationId'],
            'initialTab' => $initialTab,
        ]);
    }

    private function transformClient(Client $client): array
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

    private function buildStats(Client $client): array
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

    private function getTodaysWorkout(Client $client): ?array
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

    private function getUpcomingWorkouts(Client $client): array
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

    private function getProgramProgress(Client $client): ?array
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

    private function getRecentAchievements(Client $client, array $stats): array
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

    private function getMessagesData(Client $client, Request $request): array
    {
        $trainerId = $client->trainer_id;
        $clientUserId = $client->user_id;
        $colors = [
            'bg-orange-500', 'bg-blue-500', 'bg-green-500', 'bg-purple-500',
            'bg-indigo-500', 'bg-pink-500', 'bg-yellow-500', 'bg-red-500',
        ];

        $conversations = [];
        $totalUnread = 0;

        // 1:1 conversation with trainer
        if ($trainerId) {
            $oneToOne = Conversation::firstOrCreate(
                [
                    'trainer_id' => $trainerId,
                    'type' => 'client',
                    'target_id' => $client->id,
                ],
                ['last_message_at' => now()]
            );
            $unread = $this->getConversationUnreadCount($oneToOne, $clientUserId, 'client');
            $totalUnread += $unread;
            $lastMsg = $oneToOne->messages()->orderByDesc('created_at')->first();
            $trainer = $client->trainer;
            $trainerInitials = $trainer ? $this->initialsFromName($trainer->name) : 'T';
            $conversations[] = [
                'id' => $oneToOne->id,
                'type' => 'client',
                'name' => $trainer?->name ?? 'Trainer',
                'initials' => $trainerInitials,
                'color' => 'bg-gradient-to-br from-gray-700 to-gray-900',
                'lastMessage' => $lastMsg ? $this->messagePreview($lastMsg) : null,
                'unreadCount' => $unread,
            ];
        }

        // Group conversations
        $client->load('groups');
        foreach ($client->groups as $group) {
            $conv = Conversation::firstOrCreate(
                [
                    'trainer_id' => $group->trainer_id,
                    'type' => 'group',
                    'target_id' => $group->id,
                ],
                ['last_message_at' => now()]
            );
            $unread = $this->getConversationUnreadCount($conv, $clientUserId, 'group');
            $totalUnread += $unread;
            $lastMsg = $conv->messages()->orderByDesc('created_at')->first();
            $initials = $this->initialsFromName($group->name);
            $color = $colors[abs(crc32($group->name)) % count($colors)];
            $conversations[] = [
                'id' => $conv->id,
                'type' => 'group',
                'name' => $group->name,
                'initials' => $initials,
                'color' => $color,
                'lastMessage' => $lastMsg ? $this->messagePreview($lastMsg) : null,
                'unreadCount' => $unread,
            ];
        }

        // Selected conversation
        $requestedId = $request->has('conversation') ? (int) $request->get('conversation') : null;
        $selected = null;
        foreach ($conversations as $c) {
            if ((int) $c['id'] === $requestedId) {
                $selected = $c;
                break;
            }
        }
        if (! $selected && count($conversations) > 0) {
            $selected = $conversations[0];
        }
        $selectedConversationId = $selected ? (int) $selected['id'] : null;

        // Messages for selected conversation
        $messages = [];
        if ($selectedConversationId) {
            $conv = Conversation::find($selectedConversationId);
            if ($conv && $this->clientCanAccessConversation($client, $conv)) {
                $messages = $conv->messages()
                    ->orderBy('created_at', 'asc')
                    ->limit(100)
                    ->get()
                    ->map(fn (ConversationMessage $m) => $this->formatMessageForClient($m, $clientUserId, $conv->type))
                    ->toArray();
            }
        }

        return [
            'conversations' => $conversations,
            'messages' => $messages,
            'unreadCount' => $totalUnread,
            'conversationId' => $selectedConversationId,
            'selectedConversationId' => $selectedConversationId,
        ];
    }

    private function clientCanAccessConversation(Client $client, Conversation $conv): bool
    {
        if ($conv->trainer_id !== $client->trainer_id) {
            return false;
        }
        if ($conv->type === 'client') {
            return (int) $conv->target_id === (int) $client->id;
        }
        if ($conv->type === 'group') {
            return $client->groups()->where('group_id', $conv->target_id)->exists();
        }
        return false;
    }

    private function getConversationUnreadCount(Conversation $conv, ?int $clientUserId, string $convType): int
    {
        if (! $clientUserId) {
            return 0;
        }
        if ($convType === 'client') {
            return $conv->messages()
                ->where('sender_type', 'trainer')
                ->whereDoesntHave('reads', fn ($r) => $r->where('reader_id', $clientUserId))
                ->count();
        }
        // Group: unread = messages from trainer or other clients not read by this client
        $client = Client::where('user_id', $clientUserId)->first();
        if (! $client) {
            return 0;
        }
        return $conv->messages()
            ->where(function ($q) use ($client) {
                $q->where('sender_type', 'trainer')
                    ->orWhere(function ($q2) use ($client) {
                        $q2->where('sender_type', 'client')->where('sender_id', '!=', $client->id);
                    });
            })
            ->whereDoesntHave('reads', fn ($r) => $r->where('reader_id', $clientUserId))
            ->count();
    }

    private function initialsFromName(string $name): string
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

    private function messagePreview(ConversationMessage $m): string
    {
        if ($m->payload_type === 'text' && $m->body) {
            return \Str::limit($m->body, 50);
        }
        return match ($m->payload_type) {
            'workout' => 'Workout update',
            'image' => 'Image',
            'file' => 'File',
            'schedule' => 'Schedule',
            'audio' => 'Audio message',
            default => 'Message',
        };
    }

    private function formatMessageForClient(ConversationMessage $m, ?int $clientUserId, string $convType): array
    {
        $sender = $m->sender_type === 'trainer' ? 'trainer' : 'client';
        $read = false;
        if ($clientUserId) {
            $read = $m->reads()->where('reader_id', $clientUserId)->exists();
        }
        return [
            'id' => $m->id,
            'sender' => $sender,
            'type' => $m->payload_type ?? 'text',
            'message' => $m->body,
            'content' => $m->payload,
            'timestamp' => $m->created_at->format('g:i A'),
            'read' => $read,
        ];
    }
}
