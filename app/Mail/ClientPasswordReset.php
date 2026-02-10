<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Client $client,
        public string $plainPassword,
        public string $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        $trainerName = $this->client->trainer?->name ?? config('app.name');
        $replyTo = $this->client->trainer?->email ?? config('mail.from.address');

        return new Envelope(
            subject: 'Your new password – ' . $trainerName,
            replyTo: [$replyTo],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-password-reset',
        );
    }
}
