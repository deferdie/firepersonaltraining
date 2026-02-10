<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trainer\AssignHabitRequest;
use App\Models\Client;
use App\Models\Habit;
use App\Models\LibraryHabit;
use Illuminate\Http\RedirectResponse;

class ClientHabitController extends Controller
{
    public function store(AssignHabitRequest $request, Client $client): RedirectResponse
    {
        if ($client->trainer_id !== auth()->id()) {
            abort(403);
        }

        $trainerId = auth()->id();
        $validated = $request->validated();

        if (! empty($validated['library_habit_id'])) {
            $libraryHabit = LibraryHabit::where('trainer_id', $trainerId)
                ->findOrFail($validated['library_habit_id']);
            Habit::create([
                'trainer_id' => $trainerId,
                'assignable_type' => Client::class,
                'assignable_id' => $client->id,
                'name' => $libraryHabit->name,
                'description' => $libraryHabit->description,
                'source_library_habit_id' => $libraryHabit->id,
            ]);
        } else {
            Habit::create([
                'trainer_id' => $trainerId,
                'assignable_type' => Client::class,
                'assignable_id' => $client->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'source_library_habit_id' => null,
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Habit assigned successfully.');
    }
}
