<?php

namespace Tests\Feature\Trainer;

use App\Models\Client;
use App\Models\Habit;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientScheduleTest extends TestCase
{
    use RefreshDatabase;

    private User $trainer;

    private Client $client;

    private Habit $habit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->trainer = User::factory()->trainer()->create();
        $this->client = Client::factory()->create(['trainer_id' => $this->trainer->id]);
        $this->habit = Habit::factory()->forClient($this->client)->create();
    }

    public function test_trainer_can_list_client_schedules(): void
    {
        Schedule::factory()
            ->forClientAndHabit($this->client, $this->habit)
            ->create([
                'starts_at' => now()->addDays(2),
                'title' => 'Test Habit',
            ]);

        $response = $this->actingAs($this->trainer)
            ->getJson(route('trainer.clients.schedules.index', [
                'client' => $this->client->id,
                'start' => now()->toDateString(),
                'end' => now()->addMonth()->toDateString(),
            ]));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'Test Habit']);
    }

    public function test_trainer_can_create_schedule(): void
    {
        $startsAt = now()->addDays(3)->setTime(9, 0);

        $response = $this->actingAs($this->trainer)
            ->post(route('trainer.clients.schedules.store', ['client' => $this->client->id]), [
                'schedulable_type' => 'habit',
                'schedulable_id' => $this->habit->id,
                'title' => 'Morning Walk',
                'notes' => 'Daily reminder',
                'starts_at' => $startsAt->toDateTimeString(),
                'recurrence_mode' => 'daily',
                'recurrence_ends_at' => now()->addMonth()->toDateString(),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('schedules', [
            'assignable_id' => $this->client->id,
            'schedulable_id' => $this->habit->id,
            'title' => 'Morning Walk',
        ]);
    }

    public function test_trainer_can_update_schedule(): void
    {
        $schedule = Schedule::factory()
            ->forClientAndHabit($this->client, $this->habit)
            ->create(['title' => 'Original']);

        $response = $this->actingAs($this->trainer)
            ->patch(route('trainer.clients.schedules.update', [
                'client' => $this->client->id,
                'schedule' => $schedule->id,
            ]), [
                'title' => 'Updated Title',
                'starts_at' => $schedule->starts_at->toDateTimeString(),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_trainer_can_delete_schedule(): void
    {
        $schedule = Schedule::factory()
            ->forClientAndHabit($this->client, $this->habit)
            ->create();

        $response = $this->actingAs($this->trainer)
            ->delete(route('trainer.clients.schedules.destroy', [
                'client' => $this->client->id,
                'schedule' => $schedule->id,
            ]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
    }

    public function test_other_trainer_cannot_access_client_schedules(): void
    {
        $otherTrainer = User::factory()->trainer()->create();

        $response = $this->actingAs($otherTrainer)
            ->getJson(route('trainer.clients.schedules.index', [
                'client' => $this->client->id,
            ]));

        $response->assertStatus(403);
    }

    public function test_other_trainer_cannot_create_schedule_for_client(): void
    {
        $otherTrainer = User::factory()->trainer()->create();

        $response = $this->actingAs($otherTrainer)
            ->post(route('trainer.clients.schedules.store', ['client' => $this->client->id]), [
                'schedulable_type' => 'habit',
                'schedulable_id' => $this->habit->id,
                'title' => 'Hacked',
                'starts_at' => now()->addDay()->toDateTimeString(),
                'recurrence_mode' => 'one_off',
            ]);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_schedules(): void
    {
        $response = $this->getJson(route('trainer.clients.schedules.index', [
            'client' => $this->client->id,
        ]));

        $response->assertStatus(401);
    }

    public function test_trainer_can_list_assigned_content_by_type(): void
    {
        $response = $this->actingAs($this->trainer)
            ->getJson(route('trainer.clients.assigned-content.index', [
                'client' => $this->client->id,
            ]) . '?type=habit');

        $response->assertStatus(200);
        $response->assertJsonStructure(['items']);
        $response->assertJsonCount(1, 'items');
        $response->assertJsonFragment([
            'id' => $this->habit->id,
            'name' => $this->habit->name,
            'category' => 'habit',
        ]);
    }
}
