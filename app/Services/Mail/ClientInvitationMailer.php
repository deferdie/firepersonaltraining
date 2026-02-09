<?php

namespace App\Services\Mail;

use App\Mail\ClientSignupInvitation;
use App\Models\Client;
use Illuminate\Support\Facades\Mail;

class ClientInvitationMailer
{
    /**
     * Send the client signup invitation email.
     * Uses the default mailer when $mailer is null; when per-trainer SMTP/Gmail/Outlook
     * is added later, pass the mailer name to use that connection.
     */
    public function sendClientSignupInvitation(Client $client, string $signedUrl, ?string $mailer = null): void
    {
        $mailable = new ClientSignupInvitation($client, $signedUrl);

        if ($mailer === null) {
            Mail::to($client->email)->send($mailable);
        } else {
            Mail::mailer($mailer)->to($client->email)->send($mailable);
        }
    }
}
