<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Client\Traits\ClientDataTransformer;
use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkoutsController extends Controller
{
    use ClientDataTransformer;

    public function index(Request $request): Response
    {
        $client = Client::where('user_id', auth()->id())->firstOrFail();

        $client->load([
            'trainer',
            'programs.program',
            'workoutCompletions.workout',
        ]);

        $clientData = $this->transformClient($client);
        $stats = $this->buildStats($client);
        $layoutStats = $this->buildLayoutStats($stats);

        $todayWorkout = $this->getTodaysWorkout($client);
        $upcomingWorkouts = $this->getUpcomingWorkouts($client);

        return Inertia::render('Client/Workouts', [
            'client' => $clientData,
            'stats' => $stats,
            'todayWorkout' => $todayWorkout,
            'upcomingWorkouts' => $upcomingWorkouts,
            'layoutStats' => $layoutStats,
        ]);
    }
}

