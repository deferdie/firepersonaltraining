<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trainer\SettingsUpdateRequest;
use App\Models\TrainerSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $subscription = TrainerSubscription::firstOrCreateForUser($user);

        return Inertia::render('Trainer/Settings/Index', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'timezone' => $user->timezone ?? config('app.timezone'),
            ],
            'subscription' => [
                'plan_name' => $subscription->plan_name,
                'status' => $subscription->status,
                'renewal_date' => $subscription->renewal_date?->format('Y-m-d'),
            ],
            'timezones' => timezone_identifiers_list(),
        ]);
    }

    public function update(SettingsUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        return redirect()->route('trainer.settings.index')->with('success', 'Settings saved.');
    }
}
