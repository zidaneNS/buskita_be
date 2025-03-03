<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function dummy_user(): User
    {
        $user = User::factory()->create([
            "nim_nip" => 181221055
        ]);

        Sanctum::actingAs($user);

        return $user;
    }

    public function create_schedule(): TestResponse
    {
        return $this->postJson('api/schedule', [
            "bus_schedule" => "2025-03-04 07:00:00",
            "capacity" => 38,
            "number" => "4"
        ]);
    }

    public function test_can_create_schedule(): void
    {
        $this->dummy_user();
        $response = $this->create_schedule();

        $response->assertStatus(201);
        $this->assertDatabaseHas('schedules', [
            "bus_schedule" => "2025-03-04 07:00:00",
            "capacity" => 38,
            "number" => "4"
        ]);
    }

    public function test_can_delete_schedule(): void
    {
        $this->dummy_user();

        $schedule = Schedule::factory()->create([
            "bus_schedule" => now(),
            "capacity" => 40,
            "number" => "8"
        ]);

        $response = $this->delete('api/schedule/' . $schedule->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('schedules', [
            "id" => $schedule->id
        ]);
        $this->assertDatabaseMissing('schedule_user', [
            "schedule_id" => $schedule->id
        ]);
    }

    public function test_can_update_schedule(): void
    {
        $this->dummy_user();
        $response_create = $this->create_schedule();

        $response = $this->putJson('api/schedule/' . $response_create["id"], [
            "bus_schedule" => "2025-03-05 16:00:00",
            "capacity" => 38,
            "number" => "4"
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('schedules', [
            "id" => $response_create["id"],
            "bus_schedule" => "2025-03-05 16:00:00"
        ]);
    }
}
