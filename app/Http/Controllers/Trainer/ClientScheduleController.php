<?php

namespace App\Http\Controllers\Trainer;

use App\Enums\SchedulableType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trainer\StoreScheduleRequest;
use App\Http\Requests\Trainer\UpdateScheduleRequest;
use App\Models\Client;
use App\Models\Schedule;
use App\Services\ScheduleEventTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class ClientScheduleController extends Controller
{
    /**
     * List schedules for a client (range-filtered for calendar).
     */
    public function index(Request $request, Client $client): JsonResponse
    {
        if ($client->trainer_id !== auth()->id()) {
            abort(403);
        }

        $start = Carbon::parse($request->query('start', now()->startOfMonth()->toDateString()));
        $end = Carbon::parse($request->query('end', now()->endOfMonth()->toDateString()));

        $schedules = $client->schedules()
            ->with('schedulable')
            ->where('is_active', true)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('starts_at', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('starts_at', '<=', $end)
                            ->where(function ($q3) use ($start) {
                                $q3->whereNull('recurrence_ends_at')
                                    ->orWhere('recurrence_ends_at', '>=', $start);
                            });
                    });
            })
            ->orderBy('starts_at')
            ->get();

        $events = ScheduleEventTransformer::toCalendarEvents($schedules, $start, $end);

        return response()->json($events);
    }

    /**
     * Store a new schedule.
     */
    public function store(StoreScheduleRequest $request, Client $client): RedirectResponse|JsonResponse
    {
        if ($client->trainer_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();
        $schedulableType = SchedulableType::from($validated['schedulable_type']);
        $modelClass = $schedulableType->modelClass();

        if (! $modelClass) {
            abort(422, 'Schedulable type not supported.');
        }

        $schedulable = $modelClass::where('trainer_id', auth()->id())
            ->where('assignable_type', Client::class)
            ->where('assignable_id', $client->id)
            ->findOrFail($validated['schedulable_id']);

        $recurrenceRule = $this->buildRecurrenceRule($validated);

        Schedule::create([
            'trainer_id' => auth()->id(),
            'assignable_type' => Client::class,
            'assignable_id' => $client->id,
            'schedulable_type' => $modelClass,
            'schedulable_id' => $schedulable->id,
            'title' => $validated['title'],
            'notes' => $validated['notes'] ?? null,
            'starts_at' => Carbon::parse($validated['starts_at']),
            'ends_at' => isset($validated['ends_at']) ? Carbon::parse($validated['ends_at']) : null,
            'timezone' => $validated['timezone'] ?? 'UTC',
            'recurrence_rule' => $recurrenceRule,
            'recurrence_ends_at' => isset($validated['recurrence_ends_at'])
                ? Carbon::parse($validated['recurrence_ends_at'])->toDateString()
                : null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true], 201);
        }

        return redirect()->back()->with('success', 'Schedule created successfully.');
    }

    /**
     * Update a schedule.
     */
    public function update(UpdateScheduleRequest $request, Client $client, Schedule $schedule): RedirectResponse|JsonResponse
    {
        if ($client->trainer_id !== auth()->id() || $schedule->assignable_id !== (int) $client->id) {
            abort(403);
        }
        if ($schedule->assignable_type !== Client::class) {
            abort(404);
        }

        $validated = $request->validated();
        $updates = array_filter([
            'title' => $validated['title'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'starts_at' => isset($validated['starts_at']) ? Carbon::parse($validated['starts_at']) : null,
            'ends_at' => array_key_exists('ends_at', $validated)
                ? ($validated['ends_at'] ? Carbon::parse($validated['ends_at']) : null)
                : null,
            'timezone' => $validated['timezone'] ?? null,
            'is_active' => $validated['is_active'] ?? null,
        ], fn ($v) => $v !== null);

        if (isset($validated['recurrence_mode'])) {
            $updates['recurrence_rule'] = $this->buildRecurrenceRule(array_merge(
                $schedule->recurrence_rule ?? [],
                $validated
            ));
        }
        if (array_key_exists('recurrence_ends_at', $validated)) {
            $updates['recurrence_ends_at'] = $validated['recurrence_ends_at']
                ? Carbon::parse($validated['recurrence_ends_at'])->toDateString()
                : null;
        }

        $schedule->update($updates);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Schedule updated successfully.');
    }

    /**
     * Delete a schedule.
     */
    public function destroy(Request $request, Client $client, Schedule $schedule): RedirectResponse|JsonResponse
    {
        if ($client->trainer_id !== auth()->id() || $schedule->assignable_id !== (int) $client->id) {
            abort(403);
        }
        if ($schedule->assignable_type !== Client::class) {
            abort(404);
        }

        $schedule->delete();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Schedule deleted successfully.');
    }

    private function buildRecurrenceRule(array $input): array
    {
        return [
            'mode' => $input['recurrence_mode'] ?? 'one_off',
            'interval' => (int) ($input['recurrence_interval'] ?? 1),
            'weekdays' => $input['recurrence_weekdays'] ?? [],
        ];
    }
}
