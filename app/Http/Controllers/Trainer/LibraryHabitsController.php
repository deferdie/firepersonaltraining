<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\LibraryHabit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LibraryHabitsController extends Controller
{
    public function index(Request $request): Response
    {
        $trainerId = auth()->id();

        $query = LibraryHabit::forTrainer($trainerId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $habits = $query->orderBy('name')->paginate(12);

        $habits->getCollection()->transform(function (LibraryHabit $habit) {
            return [
                'id' => $habit->id,
                'name' => $habit->name,
                'description' => $habit->description,
                'created_at' => $habit->created_at->format('Y-m-d'),
            ];
        });

        return Inertia::render('Trainer/Library/Habits/Index', [
            'habits' => $habits,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
        ]);

        LibraryHabit::create([
            'trainer_id' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('trainer.library.habits.index')
            ->with('success', 'Habit created successfully!');
    }

    public function update(Request $request, LibraryHabit $habit): RedirectResponse
    {
        if ($habit->trainer_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
        ]);

        $habit->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('trainer.library.habits.index')
            ->with('success', 'Habit updated successfully!');
    }

    public function destroy(LibraryHabit $habit): RedirectResponse
    {
        if ($habit->trainer_id !== auth()->id()) {
            abort(403);
        }

        $habit->delete();

        return redirect()
            ->route('trainer.library.habits.index')
            ->with('success', 'Habit deleted successfully!');
    }
}
