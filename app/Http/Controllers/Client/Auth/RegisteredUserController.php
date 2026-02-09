<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(Request $request): Response|RedirectResponse
    {
        $invitation = null;

        if ($request->has('client_id') && $request->hasValidSignature()) {
            $client = Client::find($request->query('client_id'));
            if ($client && $client->user_id === null) {
                $invitation = [
                    'name' => $client->name,
                    'email' => $client->email,
                    'client_id' => $client->id,
                    'expires' => $request->query('expires'),
                    'signature' => $request->query('signature'),
                ];
            }
        }

        return Inertia::render('Client/Auth/Register', [
            'invitation' => $invitation,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $client = null;
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if ($request->filled('client_id') && $request->filled('expires') && $request->filled('signature')) {
            $signedUrl = url()->route('client.register') . '?' . http_build_query([
                'client_id' => $request->client_id,
                'expires' => $request->expires,
                'signature' => $request->signature,
            ]);
            $signedRequest = Request::create($signedUrl, 'GET');
            if (URL::hasValidSignature($signedRequest)) {
                $client = Client::find($request->client_id);
                if ($client && $client->user_id === null) {
                    $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', Rule::in([$client->email])];
                } else {
                    $client = null;
                }
            }
        }

        $validated = $request->validate($rules);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_trainer' => false,
        ]);

        if ($client) {
            $client->update(['user_id' => $user->id]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('client.dashboard', absolute: false));
    }
}
