<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientAssignedContentController extends Controller
{
    /**
     * List assigned content for a client, filtered by schedulable type.
     */
    public function index(Request $request, Client $client): JsonResponse
    {
        if ($client->trainer_id !== auth()->id()) {
            abort(403);
        }

        $type = $request->query('type', 'habit');

        $items = match ($type) {
            'habit' => $client->habits->map(fn ($h) => [
                'id' => $h->id,
                'name' => $h->name,
                'category' => 'habit',
            ])->values()->toArray(),
            default => [],
        };

        return response()->json(['items' => $items]);
    }
}
