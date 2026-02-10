<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\ActivityFeed;
use App\Models\Client;
use App\Models\ClientProgram;
use App\Models\Program;
use App\Models\TrainerNote;
use App\Models\Message;
use App\Services\ScheduleEventTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClientsController extends Controller
{
    public function index(Request $request): Response
    {
        $trainerId = auth()->id();

        // Get all clients for stats calculation
        $allClients = Client::where('trainer_id', $trainerId)->get();

        // Calculate stats
        $stats = [
            'total' => $allClients->count(),
            'active' => $allClients->where('status', 'active')->count(),
            'trial' => $allClients->where('status', 'trial')->count(),
        ];

        // Get paginated clients with search
        $query = Client::where('trainer_id', $trainerId)
            ->with(['programs.program']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('name')->paginate(12);

        // Transform clients data
        $clients->getCollection()->transform(function ($client) {
            // Get initials (first letter of each word)
            $nameParts = explode(' ', $client->name);
            $initials = '';
            foreach ($nameParts as $part) {
                if (!empty($part)) {
                    $initials .= strtoupper(substr($part, 0, 1));
                }
            }
            if (strlen($initials) > 2) {
                $initials = substr($initials, 0, 2);
            }

            // Generate consistent color based on name hash
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

            // Get current active program name
            $activeProgram = $client->programs()
                ->where('status', 'active')
                ->with('program')
                ->first();
            $programName = $activeProgram?->program?->name;

            return [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'status' => $client->status,
                'initials' => $initials,
                'color' => $color,
                'program' => $programName,
                'joinDate' => $client->created_at->format('Y-m-d'),
                'created_at' => $client->created_at,
                'has_completed_signup' => $client->user_id !== null,
            ];
        });

        // Get programs for the select dropdown
        $programs = Program::where('trainer_id', $trainerId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Trainer/Clients/Index', [
            'clients' => $clients,
            'stats' => $stats,
            'filters' => $request->only(['search']),
            'programs' => $programs,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email',
            'phone' => 'nullable|string|max:20',
            'program_id' => 'nullable|integer|exists:programs,id',
            'status' => 'required|in:active,trial,inactive',
            'goals' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Convert empty string to null for program_id
        if (isset($validated['program_id']) && $validated['program_id'] === '') {
            $validated['program_id'] = null;
        }

        // Combine first and last name
        $name = trim($validated['firstName'] . ' ' . $validated['lastName']);

        // Create client
        $client = Client::create([
            'trainer_id' => auth()->id(),
            'name' => $name,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'],
        ]);

        // Assign program if provided
        if (!empty($validated['program_id'])) {
            ClientProgram::create([
                'client_id' => $client->id,
                'program_id' => $validated['program_id'],
                'status' => 'active',
                'start_date' => now(),
            ]);
        }

        // TODO: Store goals and notes when database fields are added
        // For now, we'll skip storing these fields

        return redirect()
            ->route('trainer.clients.index')
            ->with('success', 'Client added successfully!');
    }

    public function show(Client $client): Response
    {
        // Ensure the client belongs to the authenticated trainer
        if ($client->trainer_id !== auth()->id()) {
            abort(403);
        }

        // Load relationships
        $client->load([
            'programs.program',
            'workoutCompletions.workout.exercises',
            'foodEntries' => function ($query) {
                $query->orderBy('logged_at', 'desc')->limit(50);
            },
            'progressPhotos' => function ($query) {
                $query->orderBy('taken_at', 'desc');
            },
            'habits.sourceLibraryHabit',
            'schedules' => fn ($q) => $q->where('is_active', true)->orderBy('starts_at'),
        ]);

        // Get trainer notes
        $trainerNotes = TrainerNote::where('client_id', $client->id)
            ->where('trainer_id', auth()->id())
            ->with('trainer')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get recent messages
        $trainerId = auth()->id();
        $clientUserId = $client->user_id;
        $recentMessages = collect([]);
        if ($clientUserId) {
            $recentMessages = Message::where(function ($query) use ($trainerId, $clientUserId) {
                $query->where(function ($q) use ($trainerId, $clientUserId) {
                    $q->where('sender_id', $trainerId)->where('receiver_id', $clientUserId);
                })->orWhere(function ($q) use ($trainerId, $clientUserId) {
                    $q->where('sender_id', $clientUserId)->where('receiver_id', $trainerId);
                });
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        }

        // Calculate stats
        $workoutCompletions = $client->workoutCompletions;
        $workoutsCompleted = $workoutCompletions->count();
        
        // Calculate streak (consecutive days with workouts)
        $streak = 0;
        if ($workoutsCompleted > 0) {
            $completionDates = $workoutCompletions->pluck('completed_at')
                ->map(fn($date) => $date->format('Y-m-d'))
                ->unique()
                ->sortDesc()
                ->values();
            
            $currentDate = now()->format('Y-m-d');
            $checkDate = $currentDate;
            $consecutive = 0;
            
            foreach ($completionDates as $date) {
                if ($date === $checkDate || $date === date('Y-m-d', strtotime($checkDate . ' -1 day'))) {
                    $consecutive++;
                    $checkDate = date('Y-m-d', strtotime($date . ' -1 day'));
                } else {
                    break;
                }
            }
            $streak = $consecutive;
        }

        // Calculate adherence (assuming a program with expected workouts)
        $activeProgram = $client->programs()->where('status', 'active')->with('program')->first();
        $workoutsTotal = $activeProgram ? 32 : $workoutsCompleted; // Mock total for now
        $adherence = $workoutsTotal > 0 ? round(($workoutsCompleted / $workoutsTotal) * 100, 1) : 0;

        // Calculate average duration
        $avgDuration = $workoutCompletions->where('duration_minutes', '!=', null)
            ->avg('duration_minutes') ?? 0;
        $avgDuration = round($avgDuration);

        // Calculate total hours
        $totalMinutes = $workoutCompletions->sum('duration_minutes') ?? 0;
        $totalHours = round($totalMinutes / 60, 1);

        // Get initials and color (reuse logic from index)
        $nameParts = explode(' ', $client->name);
        $initials = '';
        foreach ($nameParts as $part) {
            if (!empty($part)) {
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

        // Get active program name
        $programName = $activeProgram?->program?->name;

        // Get recent activity from ActivityFeed
        $activityFeeds = ActivityFeed::where('client_id', $client->id)
            ->with(['performedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(fn(ActivityFeed $feed) => $feed->toActivityArray())
            ->toArray();

        // Transform activity feeds to format expected by frontend
        $recentActivity = $activityFeeds;

        // Also add messages (if not already in activity feed)
        // For now, keep messages separate as they might not be in activity feed yet
        foreach ($recentMessages->take(5) as $message) {
            $recentActivity[] = [
                'type' => 'message',
                'date' => $message->created_at->format('Y-m-d'),
                'time' => $message->created_at->format('g:i A'),
                'title' => $message->sender_id === $trainerId ? 'Message to ' . $client->name : 'Message from ' . $client->name,
                'message' => $message->content,
                'performed_by' => $message->sender->name ?? 'Unknown',
            ];
        }

        // Sort by date/time descending
        usort($recentActivity, function ($a, $b) {
            $dateA = $a['date'] . ' ' . $a['time'];
            $dateB = $b['date'] . ' ' . $b['time'];
            return strtotime($dateB) - strtotime($dateA);
        });
        $recentActivity = array_slice($recentActivity, 0, 10);

        // Build weekly activity (last 7 days)
        $weeklyActivity = [];
        $days = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $dayIndex = $date->dayOfWeek;
            $dayAbbr = $days[$dayIndex];
            
            $hasWorkout = $client->workoutCompletions()
                ->whereDate('completed_at', $dateStr)
                ->exists();
            
            $isToday = $i === 0;
            $isPast = $i > 0;
            
            $weeklyActivity[] = [
                'day' => $dayAbbr,
                'date' => $date->format('M j'),
                'completed' => $hasWorkout,
                'missed' => $isPast && !$hasWorkout,
                'upcoming' => $isToday && !$hasWorkout,
            ];
        }

        // Transform food entries (grouped by date)
        $foodEntriesByDate = [];
        foreach ($client->foodEntries as $entry) {
            $date = $entry->logged_at->format('Y-m-d');
            if (!isset($foodEntriesByDate[$date])) {
                $foodEntriesByDate[$date] = [];
            }
            $foodEntriesByDate[$date][] = [
                'id' => $entry->id,
                'date' => $date,
                'time' => $entry->logged_at->format('g:i A'),
                'meal' => ucfirst($entry->meal_type ?? 'Meal'),
                'description' => $entry->food_name,
                'calories' => $entry->calories,
                'protein' => $entry->protein,
                'carbs' => $entry->carbs,
                'fats' => $entry->fat,
            ];
        }

        // Transform progress photos (grouped by date)
        $progressPhotosByDate = [];
        foreach ($client->progressPhotos as $photo) {
            $date = $photo->taken_at->format('Y-m-d');
            if (!isset($progressPhotosByDate[$date])) {
                $progressPhotosByDate[$date] = [
                    'date' => $date,
                    'weight' => $photo->weight,
                    'notes' => $photo->notes,
                    'photos' => [],
                ];
            }
            $progressPhotosByDate[$date]['photos'][] = [
                'id' => $photo->id,
                'url' => $photo->photo_url,
                'angle' => ucfirst($photo->angle ?? 'Front'),
            ];
        }

        // Transform notes
        $notes = $trainerNotes->map(function ($note) {
            return [
                'id' => $note->id,
                'date' => $note->created_at->format('Y-m-d'),
                'author' => $note->trainer->name ?? 'Trainer',
                'category' => ucfirst($note->category ?? 'General'),
                'content' => $note->content,
            ];
        })->toArray();

        // Assigned content (habits and future types)
        $assignedContent = $client->habits->map(function ($habit) {
            return [
                'id' => $habit->id,
                'category' => 'habits',
                'name' => $habit->name,
                'description' => $habit->description,
                'status' => 'active',
                'assignedDate' => $habit->created_at->format('Y-m-d'),
                'sourceLibraryHabitId' => $habit->source_library_habit_id,
            ];
        })->toArray();

        // Schedule data
        $scheduleRangeStart = Carbon::now()->startOfMonth();
        $scheduleRangeEnd = Carbon::now()->endOfMonth();
        $calendarEvents = ScheduleEventTransformer::toCalendarEvents(
            $client->schedules,
            $scheduleRangeStart,
            $scheduleRangeEnd
        );
        $upcomingSessions = ScheduleEventTransformer::toUpcomingSessions($client->schedules, 10);

        // Mock data for features not yet implemented
        $goals = []; // Will be implemented later
        $payments = []; // Will be implemented later
        $aiInsights = []; // Will be implemented later
        $smartActions = []; // Will be implemented later

        // Calculate last active (most recent activity)
        $lastActive = 'Never';
        if ($recentActivity) {
            $lastActivity = $recentActivity[0];
            $lastActivityDate = \Carbon\Carbon::parse($lastActivity['date'] . ' ' . $lastActivity['time']);
            $lastActive = $lastActivityDate->diffForHumans();
        }

        return Inertia::render('Trainer/Clients/Show', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'status' => $client->status,
                'initials' => $initials,
                'color' => $color,
                'program' => $programName,
                'joinDate' => $client->created_at->format('Y-m-d'),
                'lastActive' => $lastActive,
                'has_completed_signup' => $client->user_id !== null,
            ],
            'stats' => [
                'streak' => $streak,
                'workoutsCompleted' => $workoutsCompleted,
                'workoutsTotal' => $workoutsTotal,
                'adherence' => $adherence,
                'avgDuration' => $avgDuration,
                'totalHours' => $totalHours,
            ],
            'recentActivity' => $recentActivity,
            'weeklyActivity' => $weeklyActivity,
            'foodEntries' => $foodEntriesByDate,
            'progressPhotos' => array_values($progressPhotosByDate),
            'notes' => $notes,
            'goals' => $goals,
            'payments' => $payments,
            'aiInsights' => $aiInsights,
            'smartActions' => $smartActions,
            'assignedContent' => $assignedContent,
            'upcomingSessions' => $upcomingSessions,
            'calendarEvents' => $calendarEvents,
            'recentMessages' => $recentMessages->map(function ($msg) use ($trainerId) {
                return [
                    'id' => $msg->id,
                    'sender' => $msg->sender_id === $trainerId ? 'trainer' : 'client',
                    'message' => $msg->content,
                    'timestamp' => $msg->created_at->format('g:i A'),
                    'read' => $msg->read,
                ];
            })->toArray(),
        ]);
    }
}
