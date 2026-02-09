<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\TrainerNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClientNoteController extends Controller
{
    public function store(Request $request, Client $client): RedirectResponse
    {
        // Ensure the client belongs to the authenticated trainer
        if ($client->trainer_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'category' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        TrainerNote::create([
            'trainer_id' => auth()->id(),
            'client_id' => $client->id,
            'category' => $validated['category'] ?? null,
            'content' => $validated['content'],
        ]);

        return redirect()
            ->route('trainer.clients.show', $client)
            ->with('success', 'Note added successfully!');
    }
}
