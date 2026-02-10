<?php

namespace Tests\Feature\Trainer;

use App\Mail\ClientPasswordReset;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClientPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private User $trainer;

    private Client $clientWithUser;

    private User $clientUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->trainer = User::factory()->trainer()->create();
        $this->clientUser = User::factory()->create(['is_trainer' => false]);
        $this->clientWithUser = Client::factory()->create([
            'trainer_id' => $this->trainer->id,
            'user_id' => $this->clientUser->id,
            'email' => $this->clientUser->email,
        ]);
    }

    public function test_trainer_can_reset_client_password_and_email_is_sent(): void
    {
        Mail::fake();

        $oldPasswordHash = $this->clientUser->password;

        $response = $this->actingAs($this->trainer)
            ->post(route('trainer.clients.reset-password', $this->clientWithUser));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->clientUser->refresh();
        $this->assertNotSame($oldPasswordHash, $this->clientUser->password);

        Mail::assertSent(ClientPasswordReset::class, function (ClientPasswordReset $mail) {
            return $mail->hasTo($this->clientWithUser->email);
        });
    }

    public function test_other_trainer_cannot_reset_client_password(): void
    {
        $otherTrainer = User::factory()->trainer()->create();

        $response = $this->actingAs($otherTrainer)
            ->post(route('trainer.clients.reset-password', $this->clientWithUser));

        $response->assertStatus(403);
    }

    public function test_reset_returns_error_when_client_has_not_completed_signup(): void
    {
        $clientWithoutUser = Client::factory()->create([
            'trainer_id' => $this->trainer->id,
            'user_id' => null,
        ]);

        $response = $this->actingAs($this->trainer)
            ->post(route('trainer.clients.reset-password', $clientWithoutUser));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_guest_cannot_reset_client_password(): void
    {
        $response = $this->post(route('trainer.clients.reset-password', $this->clientWithUser));

        $response->assertStatus(401);
    }
}
