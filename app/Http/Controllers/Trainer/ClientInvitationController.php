<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\Mail\ClientInvitationMailer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;

class ClientInvitationController extends Controller
{
    public function __construct(
        private ClientInvitationMailer $mailer
    ) {}

    /**
     * Send a signup invitation email to the client.
     */
    public function store(Client $client): RedirectResponse
    {
        if ($client->trainer_id !== auth()->id()) {
            abort(403);
        }

        if ($client->user_id !== null) {
            return redirect()->back()->with('error', 'This client has already completed signup.');
        }

        $signedUrl = URL::temporarySignedRoute(
            'client.register',
            now()->addDays(7),
            ['client_id' => $client->id]
        );

        $this->mailer->sendClientSignupInvitation($client, $signedUrl);

        return redirect()->back()->with('success', 'Signup invitation sent to ' . $client->email);
    }
}
