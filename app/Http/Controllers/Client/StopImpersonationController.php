<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class StopImpersonationController extends Controller
{
    public function store(): RedirectResponse
    {
        $trainerId = session('impersonating_from_trainer_id');

        if ($trainerId === null) {
            return redirect()->route('client.dashboard');
        }

        $trainer = User::find($trainerId);

        if (!$trainer || !$trainer->is_trainer) {
            session()->forget('impersonating_from_trainer_id');
            return redirect()->route('client.dashboard');
        }

        Auth::login($trainer);
        session()->forget('impersonating_from_trainer_id');

        return redirect()->route('trainer.dashboard');
    }
}
