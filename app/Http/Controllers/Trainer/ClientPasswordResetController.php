<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\Mail\ClientPasswordResetMailer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;

class ClientPasswordResetController extends Controller
{
    public function __construct(
        private ClientPasswordResetMailer $mailer
    ) {}

    /**
     * Generate a new password for the client user and email it to them.
     */
    public function store(Client $client): RedirectResponse
    {
        if ($client->trainer_id !== auth()->id()) {
            abort(403);
        }

        if ($client->user_id === null) {
            return redirect()->back()->with('error', 'This client has not completed signup yet. Send a signup invitation first.');
        }

        $user = $client->user;
        $plainPassword = $this->generateSecurePassword();
        $user->password = $plainPassword;
        $user->save();

        $loginUrl = URL::route('client.login');
        $this->mailer->sendClientPasswordReset($client, $plainPassword, $loginUrl);

        return redirect()->back()->with('success', 'A new password has been sent to ' . $client->email);
    }

    private function generateSecurePassword(int $length = 14): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
        $max = strlen($chars) - 1;
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $max)];
        }
        return $password;
    }
}
