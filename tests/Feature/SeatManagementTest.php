<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
            "time" => now()->format('Y-m-d H:i:s'),
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
            "time" => now()->format('Y-m-d H:i:s'),
            "bus_id" => $bus->id,
            "route_id" => 1
        ]);

        $response->assertStatus(201);
        
        $schedule = Schedule::find($response['id']);

        $total_seats = $bus->available_col * $bus->available_row + $bus->available_backseat;

        $this->assertEquals(count($schedule->seats), $total_seats);
    }

    public function test_user_can_pick_seat_if_empty_and_credit_score_greater_than_10_and_schedule_not_closed(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $schedule = Schedule::find($schedule_id);

        $seat = $schedule->seats[0];

        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'user_id',
                'seat_number'
            ])
            ->assertJson([
                'user_id' => $passenger->id
            ]);
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

        $schedule = Schedule::find($schedule_id);

        $seat = $schedule->seats[2];

        $response = $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat->id
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

        $schedule = Schedule::find($schedule_id);
        $seat = $schedule->seats[0];

        $this->actingAs($passenger2)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);

        $response = $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);

        $response->assertStatus(400);
    }

    public function test_user_cannot_pick_seat_if_schedule_closed(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();

        $co_leader = User::factory()->create([
            'name' => 'test',
            'nim_nip' => 'cole'
        ]);

        $schedule = Schedule::find($schedule_id);

        $seat = $schedule->seats[1];

        $this->actingAs($co_leader)->putJson('api/schedules/' . $schedule_id, [
            "time" => $schedule->time,
            "bus_id" => $bus->id,
            "route_id" => $schedule->route_id,
            "closed" => true
        ]);

        $response = $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);

        $response->assertStatus(400);
    }

    public function test_user_can_see_seat_list(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($passenger)->get('api/seats/schedule/' . $schedule_id);

        $response
            ->assertStatus(200)
            ->assertJsonCount($bus->available_row * $bus->available_col + $bus->available_backseat)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'user_id',
                    'verified',
                    'seat_number'
                ]
            ]);
    }

    public function test_user_only_can_pick_one_seat_in_one_schedule(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();

        $seat1 = Schedule::find($schedule_id)->seats[1];
        $seat2 = Schedule::find($schedule_id)->seats[2];

        $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat1->id
        ]);

        $response = $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat2->id
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

        $seat = Schedule::find($schedule_id)->seats[0];

        $passenger_seat = $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);

        $response = $this->actingAs($co_leader)->delete('api/seats/' . $seat->id);

        $response->assertStatus(204);
        $this
            ->assertDatabaseMissing('schedule_user', [
                'schedule_id' => $schedule_id,
                'user_id' => $passenger->id
            ])
            ->assertDatabaseHas('seats', [
                'id' => $seat->id,
                'user_id' => null
            ]);
    }

    public function test_user_can_cancel_their_seat(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();

        $seat = Schedule::find($schedule_id)->seats[1];

        $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);

        $response = $this->actingAs($passenger)->deleteJson('api/seats/' . $seat->id);

        $response->assertStatus(204);

        $this
            ->assertDatabaseMissing('schedule_user', [
                'schedule_id' => $schedule_id,
                'user_id' => $passenger->id
            ])
            ->assertDatabaseHas('seats', [
                'id' => $seat->id,
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

        $seat = Schedule::find($schedule_id)->seats[0];

        $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);

        $response = $this->actingAs($passenger2)->delete('api/seats/' . $seat->id);

        $response->assertStatus(403);
    }

    public function test_user_can_change_their_seat_position(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger = $this->dummy_passenger();

        $schedule = Schedule::find($schedule_id);

        $seat = $schedule->seats[0];
        $new_seat = $schedule->seats[1];

        $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);

        $response = $this->actingAs($passenger)->putJson('api/seats/' . $seat->id, [
            'new_seat_id' => $new_seat->id
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_id',
                'verified',
                'seat_number'
            ]);
        $this
            ->assertDatabaseHas('seats', [
                'id' => $seat->id,
                'user_id' => null
            ])
            ->assertDatabaseHas('seats', [
                'id' => $new_seat->id,
                'user_id' => $passenger->id
            ]);
    }

    public function test_user_cannot_change_their_seat_to_occupied_seat(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $passenger1 = $this->dummy_passenger();
        $passenger2 = User::factory()->create([
            'name' => 'test',
            'nim_nip' => '123',
            'role_id' => 3
        ]);

        $schedule = Schedule::find($schedule_id);

        $seat = $schedule->seats[0];
        $new_seat = $schedule->seats[1];

        $this->actingAs($passenger1)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);

        $response = $this->actingAs($passenger2)->putJson('api/seats/' . $seat->id, [
            'new_seat_id' => $new_seat->id
        ]);

        $response->assertStatus(400);
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

        $seat = Schedule::find($schedule_id)->seats[0];

        $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);

        $response = $this->actingAs($co_leader)->get('api/seats/' . $seat->id . '/verify');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_id',
                'verified',
                'seat_number'
            ]);
        $this->assertDatabaseHas('seats', [
            'id' => $seat->id,
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

        $seat = Schedule::find($schedule_id)->seats[0];

        $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);

        $response = $this->actingAs($passenger2)->get('api/seats/' . $seat->id . '/verify');
        $response->assertStatus(403);
    }

    public function test_user_can_get_their_schedule(): void
    {
        $bus = $this->dummy_bus();

        $schedule_id = $this->dummy_schedule_id($bus->id);

        $schedule = Schedule::find($schedule_id);

        $seat = $schedule->seats[0];

        $passenger = $this->dummy_passenger();

        $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);

        $response = $this->actingAs($passenger)->get('api/user/schedules');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'time',
                    'bus_identity',
                    'route_name'
                ]
            ])
            ->assertJsonCount(1);
    }
}
