<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function dummyUser(): User
    {
        $user = User::factory()->create([
            "nim_nip" => 181221055
        ]);

        Sanctum::actingAs($user);

        return $user;
    }
    public function test_can_create_schedule(): void
    {
        $this->dummyUser();
        $response = $this->postJson('api/schedule', [
            "bus_schedule" => "2025-03-04 07:00:00",
            "capacity" => 38,
            "number" => "4"
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('schedules', [
            "bus_schedule" => "2025-03-04 07:00:00",
            "capacity" => 38,
            "number" => "4"
        ]);
    }

    public function test_can_delete_schedule(): void
    {
        $schedule = Schedule::factory()->create([
            "bus_schedule" => now(),
            "capacity" => 40,
            "number" => "8"
        ]);

        
    }
}
