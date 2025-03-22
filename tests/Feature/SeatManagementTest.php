<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SeatManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function dummy_passenger(): User
    {
        $user = User::factory()->create([
            'name' => 'zidane1',
            'nim_nip' => '181221056',
            'role' => 'passenger'
        ]);

        Sanctum::actingAs($user);

        return $user;
    }

    public function dummy_co_leader(): User
    {
        $user = User::factory()->create([
            'name' => 'zidane',
            'nim_nip' => '181221055'
        ]);

        Sanctum::actingAs($user);

        return $user;
    }

    public function dummy_bus($identity = '8'): Bus
    {
        return Bus::factory()->create([
            'identity' => $identity
        ]);
    }

    public function test_seat_created_when_schedule_created(): void
    {
        $this->dummy_co_leader();

        $bus = $this->dummy_bus();

        $response = $this->postJson('api/schedules', [
            "bus_schedule" => now()->format('Y-m-d H:i:s'),
            "bus_id" => $bus->id,
            "route_id" => 1
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('seats', [
            'bus_id' => $bus->id,
            'row_position' => 1,
            'col_position' => 1
        ]);
        $this->assertDatabaseHas('seats', [
            'bus_id' => $bus->id,
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col
        ]);
        $this->assertDatabaseHas('seats', [
            'bus_id' => $bus->id,
            'backseat_position' => $bus->available_backseat
        ]);
        $this->assertDatabaseMissing('seats', [
            'bus_id' => $bus->id,
            'row_position' => $bus->available_row + 1
        ]);
        $this->assertDatabaseMissing('seats', [
            'bus_id' => $bus->id,
            'backseat_position' => $bus->available_backseat + 1
        ]);
    }

    public function test_user_can_pick_seat_if_empty_and_credit_score_greater_than_10(): void
    {
        $user = $this->dummy_co_leader();

        $bus = $this->dummy_bus();

        $schedule_response = $this->postJson('api/schedules', [
            "bus_schedule" => now()->format('Y-m-d H:i:s'),
            "bus_id" => $bus->id,
            "route_id" => 1
        ]);

        $response = $this->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_response['id'],
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('schedule_user', [
            'schedule_id' => $schedule_response['id'],
            'user_id' => $bus->id
        ]);
        $this->assertDatabaseHas('seats', [
            'bus_id' => $bus->id,
            'user_id' => $user->id
        ]);
    }
}
