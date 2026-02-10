<?php

namespace App\Services\Mail;

use App\Mail\ClientPasswordReset;
use App\Models\Client;
use Illuminate\Support\Facades\Mail;

class ClientPasswordResetMailer
{
    /**
     * Send the client their new password and login link.
     */
    public function sendClientPasswordReset(Client $client, string $plainPassword, string $loginUrl, ?string $mailer = null): void
    {
        $mailable = new ClientPasswordReset($client, $plainPassword, $loginUrl);

        if ($mailer === null) {
            Mail::to($client->email)->send($mailable);
        } else {
            Mail::mailer($mailer)->to($client->email)->send($mailable);
        }
    }
}
