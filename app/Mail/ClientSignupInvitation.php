<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientSignupInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Client $client,
        public string $signedUrl,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $trainerName = $this->client->trainer?->name ?? config('app.name');

        $replyTo = $this->client->trainer?->email ?? config('mail.from.address');

        return new Envelope(
            subject: 'Complete your setup – ' . $trainerName . ' invited you',
            replyTo: [$replyTo],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.client-signup-invitation',
        );
    }
}
