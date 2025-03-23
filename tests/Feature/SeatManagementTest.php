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
            'role_id' => 3
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

    public function dummy_schedule_id($bus_id): int
    {
        $co_leader = $this->dummy_co_leader();

        $response = $this->actingAs($co_leader)->postJson('api/schedules', [
            "bus_schedule" => now()->format('Y-m-d H:i:s'),
            "bus_id" => $bus_id,
            "route_id" => 1
        ]);

        return $response['id'];
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
            'col_position' => $bus->available_col,
            'schedule_id' => $response['id']
        ]);
        $this->assertDatabaseHas('seats', [
            'bus_id' => $bus->id,
            'backseat_position' => $bus->available_backseat,
            'schedule_id' => $response['id']
        ]);
        $this->assertDatabaseMissing('seats', [
            'bus_id' => $bus->id,
            'row_position' => $bus->available_row + 1,
            'schedule_id' => $response['id']
        ]);
        $this->assertDatabaseMissing('seats', [
            'bus_id' => $bus->id,
            'backseat_position' => $bus->available_backseat + 1,
            'schedule_id' => $response['id']
        ]);
    }

    public function test_user_can_pick_seat_if_empty_and_credit_score_greater_than_10(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_id,
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response->assertStatus(200);
        $this
            ->assertDatabaseHas('schedule_user', [
                'schedule_id' => $schedule_id,
                'user_id' => $passenger->id
            ])
            ->assertDatabaseHas('seats', [
                'bus_id' => $bus->id,
                'user_id' => $passenger->id,
                'schedule_id' => $schedule_id
            ]);
    }

    public function test_user_cannot_pick_seat_if_credit_score_less_than_10(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = User::factory()->create([
            'name' => 'test',
            'nim_nip' => '123',
            'role_id' => 3,
            'credit_score' => 9
        ]);

        $response = $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_id,
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response->assertStatus(400);
    }

    public function test_user_cannot_pick_seat_if_seat_occupied(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();
        $passenger2 = User::factory()->create([
            'name' => 'test',
            'nim_nip' => '123',
            'role_id' => 3
        ]);

        $this->actingAs($passenger2)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_id,
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response = $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_id,
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response->assertStatus(400);
    }

    public function test_user_can_see_seat_list(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($passenger)->postJson('api/seats/schedule', [
            'schedule_id' => $schedule_id
        ]);

        $response->assertJsonCount($bus->available_row * $bus->available_col + $bus->available_backseat);
    }

    public function test_user_only_can_pick_one_seat_in_one_schedule(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();

        $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_id,
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response = $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_id,
            'row_position' => $bus->available_row - 1,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response->assertStatus(400);
    }

    public function test_co_co_leader_can_remove_user_from_their_seat(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $co_leader = User::factory()->create([
            'name' => 'test',
            'nim_nip' => '123'
        ]);

        $passenger = $this->dummy_passenger();

        $passenger_seat = $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_id,
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response = $this->actingAs($co_leader)->deleteJson('api/seats/' . $passenger_seat['id']);

        $response->assertStatus(204);
        $this
            ->assertDatabaseMissing('schedule_user', [
                'schedule_id' => $schedule_id,
                'user_id' => $passenger->id
            ])
            ->assertDatabaseHas('seats', [
                'id' => $passenger_seat['id'],
                'user_id' => null
            ]);
    }

    public function test_user_can_cancel_their_seat(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();

        $passenger_seat = $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_id,
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response = $this->actingAs($passenger)->deleteJson('api/seats/' . $passenger_seat['id']);

        $response->assertStatus(204);

        $this
            ->assertDatabaseMissing('schedule_user', [
                'schedule_id' => $schedule_id,
                'user_id' => $passenger->id
            ])
            ->assertDatabaseHas('seats', [
                'id' => $passenger_seat['id'],
                'user_id' => null
            ]);
    }

    public function test_passenger_cannot_cancel_other_passengers_seat(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();
        $passenger2 = User::factory()->create([
            'name' => 'test',
            'nim_nip' => '123',
            'role_id' => 3
        ]);

        $passenger_seat = $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_id,
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response = $this->actingAs($passenger2)->delete('api/seats/' . $passenger_seat['id']);

        $response->assertStatus(403);
    }

    public function test_user_can_change_their_seat_position(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();

        $passenger_seat = $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_id,
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response = $this->actingAs($passenger)->putJson('api/seats/' . $passenger_seat['id'], [
            'schedule_id' => $schedule_id,
            'row_position' => $bus->available_row - 3,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response->assertStatus(200);
        $this
            ->assertDatabaseHas('seats', [
                'id' => $passenger_seat['id'],
                'user_id' => null
            ])
            ->assertDatabaseHas('seats', [
                'schedule_id' => $schedule_id,
                'row_position' => $bus->available_row - 3,
                'col_position' => $bus->available_col,
                'backseat_position' => 0,
                'user_id' => $passenger->id
            ]);
    }

    public function test_co_co_leader_can_verify_user(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();
        $co_leader = User::factory()->create([
            'name' => 'test',
            'nim_nip' => '123'
        ]);

        $passenger_seat = $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_id,
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response = $this->actingAs($co_leader)->get('api/seats/' . $passenger_seat['id'] . '/verify');

        $response->assertStatus(200);
        $this->assertDatabaseHas('seats', [
            'id' => $passenger_seat['id'],
            'verified' => true
        ]);
    }

    public function test_passenger_cannot_verify_another_passenger(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();
        $passenger2 = User::factory()->create([
            'name' => 'test',
            'nim_nip' => '123',
            'role_id' => 3
        ]);

        $passenger_seat = $this->actingAs($passenger)->postJson('api/seats', [
            'bus_id' => $bus->id,
            'schedule_id' => $schedule_id,
            'row_position' => $bus->available_row,
            'col_position' => $bus->available_col,
            'backseat_position' => 0
        ]);

        $response = $this->actingAs($passenger2)->get('api/seats/' . $passenger_seat['id'] . '/verify');
        $response->assertStatus(403);
    }
}
