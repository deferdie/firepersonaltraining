<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\ActivityFeed;
use App\Models\Client;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GroupsController extends Controller
{
    public function index(Request $request): Response
    {
        $trainerId = auth()->id();

        // Get all groups for stats calculation
        $allGroups = Group::where('trainer_id', $trainerId)
            ->withCount('clients')
            ->get();

        // Calculate stats
        $stats = [
            'total' => $allGroups->count(),
            'active' => $allGroups->count(), // All groups are considered active for now
            'total_members' => $allGroups->sum('clients_count'),
        ];

        // Get paginated groups with search
        $query = Group::where('trainer_id', $trainerId)
            ->withCount('clients');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $groups = $query->orderBy('name')->paginate(12);

        // Transform groups data
        $groups->getCollection()->transform(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'members_count' => $group->clients_count,
                'created_at' => $group->created_at->format('Y-m-d'),
            ];
        });

        return Inertia::render('Trainer/Groups/Index', [
            'groups' => $groups,
            'stats' => $stats,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        Group::create([
            'trainer_id' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('trainer.groups.index')
            ->with('success', 'Group created successfully!');
    }

    public function show(Group $group): Response
    {
        if ($group->trainer_id !== auth()->id()) {
            abort(403);
        }

        $group->load(['clients.programs.program', 'clients.workoutCompletions']);

        $clientIds = $group->clients->pluck('id')->toArray();
        $colors = [
            'bg-orange-500', 'bg-blue-500', 'bg-green-500', 'bg-purple-500',
            'bg-indigo-500', 'bg-pink-500', 'bg-yellow-500', 'bg-red-500',
        ];

        // Transform members with adherence
        $members = $group->clients->map(function ($client) use ($colors) {
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
            $colorIndex = abs(crc32($client->name)) % count($colors);
            $color = $colors[$colorIndex];

            $workoutsCompleted = $client->workoutCompletions->count();
            $activeProgram = $client->programs()->where('status', 'active')->first();
            $workoutsTotal = $activeProgram ? 32 : max($workoutsCompleted, 1);
            $adherence = $workoutsTotal > 0 ? round(($workoutsCompleted / $workoutsTotal) * 100, 1) : 0;

            return [
                'id' => $client->id,
                'name' => $client->name,
                'initials' => $initials,
                'color' => $color,
                'status' => $client->status,
                'adherence' => $adherence,
            ];
        })->values()->toArray();

        // Stats
        $totalWorkouts = $group->clients->sum(fn ($c) => $c->workoutCompletions->count());
        $workoutsLast7Days = $group->clients->sum(function ($c) {
            return $c->workoutCompletions->filter(fn ($wc) => $wc->completed_at >= now()->subDays(7))->count();
        });
        $adherences = collect($members)->pluck('adherence')->filter(fn ($a) => $a > 0);
        $avgAdherence = $adherences->isEmpty() ? 0 : round($adherences->avg(), 1);
        $activeMembers = collect($members)->whereIn('status', ['active'])->count();

        $stats = [
            'avg_adherence' => $avgAdherence,
            'active_members' => $activeMembers,
            'total_workouts' => $totalWorkouts,
            'avg_workouts_per_week' => round($workoutsLast7Days, 1),
        ];

        // Recent activity
        $activityFeeds = ActivityFeed::whereIn('client_id', $clientIds)
            ->with(['client', 'performedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $recentActivity = $activityFeeds->map(function ($feed) {
            $arr = $feed->toActivityArray();
            $arr['member'] = $feed->client?->name ?? 'Unknown';
            return $arr;
        })->toArray();

        // AI insights (mock)
        $aiInsights = [
            [
                'type' => 'success',
                'icon' => 'TrendingUp',
                'title' => 'Group Momentum Strong',
                'message' => 'Average adherence at ' . $avgAdherence . '%. ' . $activeMembers . ' of ' . count($members) . ' members are actively engaged.',
                'action' => 'Send group message',
            ],
        ];
        if ($activeMembers < count($members) && count($members) > 0) {
            $aiInsights[] = [
                'type' => 'alert',
                'icon' => 'Bell',
                'title' => 'Check-in Needed',
                'message' => (count($members) - $activeMembers) . ' member(s) may need attention. Consider reaching out.',
                'action' => 'Send message',
            ];
        }
        $aiInsights[] = [
            'type' => 'suggestion',
            'icon' => 'Brain',
            'title' => 'Progress Review Time',
            'message' => 'Schedule progress assessments for group members.',
            'action' => 'Schedule assessments',
        ];

        $groupColorIndex = abs(crc32($group->name)) % count($colors);
        $groupColor = $colors[$groupColorIndex];

        // Available clients (trainer clients not already in group)
        $existingClientIds = $group->clients->pluck('id')->toArray();
        $availableClients = Client::where('trainer_id', auth()->id())
            ->whereNotIn('id', $existingClientIds)
            ->orderBy('name')
            ->get()
            ->map(function ($client) use ($colors) {
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
                $colorIndex = abs(crc32($client->name)) % count($colors);
                $color = $colors[$colorIndex];

                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'initials' => $initials,
                    'color' => $color,
                    'status' => $client->status,
                ];
            })
            ->values()
            ->toArray();

        return Inertia::render('Trainer/Groups/Show', [
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'members_count' => $group->clients->count(),
                'created_at' => $group->created_at->format('Y-m-d'),
                'color' => $groupColor,
            ],
            'stats' => $stats,
            'members' => $members,
            'availableClients' => $availableClients,
            'recentActivity' => $recentActivity,
            'assignedContent' => [],
            'aiInsights' => $aiInsights,
        ]);
    }

    public function storeMembers(Request $request, Group $group): RedirectResponse
    {
        if ($group->trainer_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'client_ids' => 'required|array',
            'client_ids.*' => 'integer|exists:clients,id',
        ]);

        $clientIds = $validated['client_ids'];
        $trainerId = auth()->id();

        // Ensure all clients belong to the trainer
        $validClientIds = Client::where('trainer_id', $trainerId)
            ->whereIn('id', $clientIds)
            ->pluck('id')
            ->toArray();

        // Exclude clients already in the group
        $existingIds = $group->clients->pluck('id')->toArray();
        $newClientIds = array_diff($validClientIds, $existingIds);

        if (!empty($newClientIds)) {
            $group->clients()->attach($newClientIds);
            Client::whereIn('id', $newClientIds)->update(['status' => 'active']);
        }

        return redirect()
            ->route('trainer.groups.show', $group)
            ->with('success', count($newClientIds) > 0
                ? count($newClientIds) . ' member(s) added successfully.'
                : 'No new members to add.');
    }

    public function destroyMember(Group $group, Client $client): RedirectResponse
    {
        if ($group->trainer_id !== auth()->id()) {
            abort(403);
        }

        if ($client->trainer_id !== auth()->id()) {
            abort(403);
        }

        $group->clients()->detach($client->id);

        return redirect()
            ->route('trainer.groups.show', $group)
            ->with('success', $client->name . ' has been removed from the group.');
    }
}
