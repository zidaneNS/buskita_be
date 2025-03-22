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

        return $user;
    }

    public function dummy_co_leader(): User
    {
        $user = User::factory()->create([
            'name' => 'zidane',
            'nim_nip' => '181221055'
        ]);

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
        $co_leader = $this->dummy_co_leader();

        $bus = $this->dummy_bus();

        $response = $this->actingAs($co_leader)->postJson('api/schedules', [
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
        $co_leader =$this->dummy_co_leader();

        $bus = $this->dummy_bus();

        $schedule_response = $this->actingAs($co_leader)->postJson('api/schedules', [
            "bus_schedule" => now()->format('Y-m-d H:i:s'),
            "bus_id" => $bus->id,
            "route_id" => 1
        ]);

        $this->actingAs($co_leader)->get('api/logout');

        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_response['id'],
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response->assertStatus(200);
        $this
            ->assertDatabaseHas('schedule_user', [
                'schedule_id' => $schedule_response['id'],
                'user_id' => $passenger->id
            ])
            ->assertDatabaseHas('seats', [
                'bus_id' => $bus->id,
                'user_id' => $passenger->id
            ]);
    }

    public function test_user_cannot_pick_seat_if_credit_score_less_than_10(): void
    {
        $co_leader = $this->dummy_co_leader();

        $bus = $this->dummy_bus();

        $schedule_response = $this->actingAs($co_leader)->postJson('api/schedules', [
            "bus_schedule" => now()->format('Y-m-d H:i:s'),
            "bus_id" => $bus->id,
            "route_id" => 1
        ]);

        $passenger = User::factory()->create([
            'name' => 'test',
            'nim_nip' => '123',
            'role' => 'passenger',
            'credit_score' => 9
        ]);

        $response = $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_response['id'],
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response->assertStatus(400);
    }

    public function test_user_cannot_pick_seat_if_seat_occupied(): void
    {
        $co_leader = $this->dummy_co_leader();

        $bus = $this->dummy_bus();

        $schedule_response = $this->actingAs($co_leader)->postJson('api/schedules', [
            "bus_schedule" => now()->format('Y-m-d H:i:s'),
            "bus_id" => $bus->id,
            "route_id" => 1
        ]);

        $passenger = $this->dummy_passenger();

        $this->actingAs($co_leader)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_response['id'],
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response = $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_response['id'],
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response->assertStatus(400);
    }

    public function test_user_can_see_seat_list(): void
    {
        $co_leader = $this->dummy_co_leader();

        $bus = $this->dummy_bus();

        $schedule_response = $this->actingAs($co_leader)->postJson('api/schedules', [
            "bus_schedule" => now()->format('Y-m-d H:i:s'),
            "bus_id" => $bus->id,
            "route_id" => 1
        ]);

        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($passenger)->postJson('api/seats/schedule', [
            'schedule_id' => $schedule_response['id']
        ]);

        $response->assertJsonCount($bus->available_row * $bus->available_col + $bus->available_backseat);
    }
}
