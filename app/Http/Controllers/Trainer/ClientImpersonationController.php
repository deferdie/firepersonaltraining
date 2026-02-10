<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ClientImpersonationController extends Controller
{
    public function store(Client $client): RedirectResponse
    {
        if ($client->trainer_id !== auth()->id()) {
            abort(403);
        }

        if ($client->user_id === null) {
            return redirect()
                ->back()
                ->with('error', 'Client has not completed signup and cannot be impersonated.');
        }

        $client->load('user');

        session(['impersonating_from_trainer_id' => auth()->id()]);
        Auth::login($client->user);

        return redirect()->route('client.dashboard');
    }
}
