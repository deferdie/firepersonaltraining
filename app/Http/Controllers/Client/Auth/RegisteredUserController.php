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
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(Request $request): Response|RedirectResponse
    {
        $invitation = null;
        $invitationLinkUsed = false;

        if ($request->has('client_id') && $request->hasValidSignature()) {
            $client = Client::find($request->query('client_id'));
            if ($client) {
                if ($client->user_id === null) {
                    $invitation = [
                        'name' => $client->name,
                        'email' => $client->email,
                        'client_id' => $client->id,
                        'expires' => $request->query('expires'),
                        'signature' => $request->query('signature'),
                    ];
                } else {
                    $invitationLinkUsed = true;
                }
            }
        }

        return Inertia::render('Client/Auth/Register', [
            'invitation' => $invitation,
            'invitationLinkUsed' => $invitationLinkUsed,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $client = null;

        if ($request->filled('client_id') && $request->filled('expires') && $request->filled('signature')) {
            $signedUrl = url()->route('client.register') . '?' . http_build_query([
                'client_id' => $request->client_id,
                'expires' => $request->expires,
                'signature' => $request->signature,
            ]);
            $signedRequest = Request::create($signedUrl, 'GET');
            if (URL::hasValidSignature($signedRequest)) {
                $client = Client::find($request->client_id);
                if ($client) {
                    if ($client->user_id !== null) {
                        return redirect()->route('client.register', $request->only('client_id', 'expires', 'signature'))
                            ->with('error', 'This signup link has already been used. Please log in if you have an account.');
                    }
                    // $client is valid and has no user; will use invitation flow below
                }
            }
        }

        if ($client) {
            $validated = $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $user = User::create([
                'name' => $client->name,
                'email' => $client->email,
                'password' => Hash::make($validated['password']),
                'is_trainer' => false,
            ]);
            $client->update(['user_id' => $user->id]);
        } else {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_trainer' => false,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('client.dashboard', absolute: false));
    }
}
