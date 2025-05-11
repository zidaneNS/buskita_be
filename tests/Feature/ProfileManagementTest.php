<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function dummy_co_leader(): User
    {
        return User::factory()->create([
            'name' => 'co_leader',
            'nim_nip' => 'co_leader',
        ]);
    }

    public function dummy_co(): User
    {
        return User::factory()->create([
            'name' => 'co',
            'nim_nip' => 'co',
            'role_id' => 2
        ]);
    }

    public function dummy_passenger(): User
    {
        return User::factory()->create([
            'name' => 'passenger',
            'nim_nip' => 'passenger',
            'role_id' => 3
        ]);
    }

    public function create_co($amount = 5): void
    {
        for ($i = 1; $i <= $amount; $i++) {
            User::factory()->create([
                'name' => "co$i",
                'nim_nip' => "co$i",
                'role_id' => 2
            ]);
        }
    }

    public function create_passenger($amount = 5): void
    {
        for ($i = 1; $i <= $amount; $i++) {
            User::factory()->create([
                'name' => "passenger$i",
                'nim_nip' => "passenger$i",
                'role_id' => 3
            ]);
        }
    }

    public function dummy_bus($identity = '8'): Bus
    {
        return Bus::factory()->create([
            'identity' => $identity
        ]);
    }

    public function dummy_schedule_id($bus_id, $time): int
    {
        $co_leader = $this->dummy_co_leader();

        $response = $this->actingAs($co_leader)->postJson('api/schedules', [
            "time" => $time,
            "bus_id" => $bus_id,
            "route_id" => 1
        ]);

        return $response['id'];
    }

    public function test_co_leader_can_see_all_users(): void
    {
        $co_leader = $this->dummy_co_leader();

        $this->create_co();
        $this->create_passenger();

        $response = $this->actingAs($co_leader)->get('api/users');

        $response
            ->assertStatus(200)
            ->assertJsonCount(14)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'nim_nip',
                    'email',
                    'role_name',
                    'phone_number',
                    'address',
                    'credit_score'
                ]
            ]);
    }

    public function test_co_can_see_all_passengers(): void
    {
        $co = $this->dummy_co();

        $this->create_passenger(10);

        $response = $this->actingAs($co)->get('api/passengers');

        $response
            ->assertStatus(200)
            ->assertJsonCount(11)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'nim_nip',
                    'email',
                    'role_name',
                    'phone_number',
                    'address',
                    'credit_score'
                ]
            ]);
    }

    public function test_co_cannot_see_all_users(): void
    {
        $co = $this->dummy_co();

        $response = $this->actingAs($co)->get('api/users');

        $response->assertStatus(403);
    }

    public function test_passenger_cannot_see_all_users(): void
    {
        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($passenger)->get('api/users');

        $response->assertStatus(403);
    }

    public function test_passenger_cannot_see_all_co(): void
    {
        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($passenger)->get('api/co');

        $response->assertStatus(403);
    }

    public function test_passenger_cannot_see_all_passenger(): void
    {
        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($passenger)->get('api/passengers');

        $response->assertStatus(403);
    }

    public function test_co_co_leader_can_see_users_profile(): void
    {
        $co_leader = $this->dummy_co_leader();

        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($co_leader)->get('api/users/' . $passenger->id);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'nim_nip',
                'name',
                'email',
                'address',
                'phone_number',
                'role_name',
                'credit_score'
            ]);
    }

    public function test_passenger_cannot_see_other_passenger_profile(): void
    {
        $passenger1 = $this->dummy_passenger();
        $passenger2 = User::factory()->create([
            'name' => 'passenger2',
            'nim_nip' => '123',
            'role_id' => 3
        ]);

        $response = $this->actingAs($passenger1)->get('api/users/' . $passenger2->id);

        $response->assertStatus(403);
    }

    public function test_co_leader_can_add_co(): void
    {
        $co_leader = $this->dummy_co_leader();

        $response = $this->actingAs($co_leader)->postJson('api/users', [
            'name' => 'test_co',
            'nim_nip' => 'test_co123',
            'password' => 'password',
            'password_confirmation' => 'password',
            'email' => 'testco@email.com',
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber()
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'nim_nip',
                'name',
                'email',
                'address',
                'phone_number',
                'role_name',
                'credit_score'
            ]);
        $this->assertDatabaseHas('users', [
            'id' => $response['id'],
            'role_id' => 2
        ]);
    }

    public function test_only_co_leader_can_add_co(): void
    {
        $co = $this->dummy_co();

        $response = $this->actingAs($co)->postJson('api/users', [
            'name' => 'test_co',
            'nim_nip' => 'test_co123',
            'password' => 'password',
            'password_confirmation' => 'password',
            'email' => 'testco@email.com',
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber()
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_update_their_profile(): void
    {
        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($passenger)->putJson('api/users/' . $passenger->id, [
            'nim_nip' => $passenger->nim_nip,
            'name' => 'new name',
            'email' => $passenger->email,
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber()
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'nim_nip',
                'name',
                'email',
                'address',
                'phone_number',
                'role_name',
                'credit_score'
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $passenger->id,
            'name' => 'new name'
        ]);
    }

    public function test_other_user_cannot_update_other_user_profile(): void
    {
        $passenger1 = $this->dummy_passenger();
        $passenger2 = User::factory()->create([
            'name' => 'passenger2',
            'nim_nip' => '123',
            'role_id' => 3
        ]);

        $response = $this->actingAs($passenger2)->putJson('api/users/' . $passenger1->id, [
            'nim_nip' => $passenger1->nim_nip,
            'name' => 'new name',
            'email' => $passenger1->email,
            'address' => fake()->address(),
            'phone_number' => fake()->phoneNumber(),
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_profile(): void
    {
        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($passenger)->delete('api/users/' . $passenger->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', [
            'id' => $passenger->id
        ]);
    }

    public function test_other_user_cannot_delete_other_user_profile(): void
    {
        $passenger1 = $this->dummy_passenger();
        $passenger2 = User::factory()->create([
            'name' => 'passenger2',
            'nim_nip' => '123',
            'role_id' => 3
        ]);

        $response = $this->actingAs($passenger2)->delete('api/users/' . $passenger1->id);

        $response->assertStatus(403);
    }

    public function test_user_not_losing_credit_score_id_verified(): void
    {
        $passenger = $this->dummy_passenger();
        $co = $this->dummy_co();

        $bus = $this->dummy_bus();

        $dt = Carbon::parse(now());

        $schedule_id = $this->dummy_schedule_id($bus->id, $dt);

        $seat = Schedule::find($schedule_id)->seats[0];

        $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);
        
        $this->actingAs($co)->get('api/seats/' . $seat->id . '/verify');

        Carbon::setTestNow(now()->addHour(2));
        
        $this->actingAs($passenger)->get('api/schedules');

        $this->assertDatabaseHas('users', [
            'id' => $passenger->id,
            'credit_score' => 15
        ]);
    }

    public function test_user_lost_5_credit_score_if_schedule_not_verified(): void
    {
        $passenger = $this->dummy_passenger();

        $bus = $this->dummy_bus();

        $dt = Carbon::parse(now());

        $schedule_id = $this->dummy_schedule_id($bus->id, $dt);

        $seat = Schedule::find($schedule_id)->seats[0];

        $this->actingAs($passenger)->postJson('api/seats', [
            'seat_id' => $seat->id
        ]);
        

        Carbon::setTestNow(now()->addHour(2));
        
        $this->actingAs($passenger)->get('api/schedules');

        $this->assertDatabaseHas('users', [
            'id' => $passenger->id,
            'credit_score' => 10
        ]);
    }

    public function test_credit_score_increase_1_point_every_day_if_credit_score_less_than_15(): void
    {
        $passenger = User::factory()->create([
            'name' => 'passenger',
            'nim_nip' => '123',
            'role_id' => 3,
            'credit_score' => 14
        ]);

        Carbon::setTestNow(now()->addDay()->addHour());

        $this->actingAs($passenger)->get('api/schedules');

        $this->assertDatabaseHas('users', [
            'id' => $passenger->id,
            'credit_score' => 15
        ]);
    }

    public function test_credit_score_not_increase_if_credit_score_15(): void
    {
        $passenger = $this->dummy_passenger();

        Carbon::setTestNow(now()->addDay()->addHour());

        $this->actingAs($passenger)->get('api/schedules');

        $this->assertDatabaseHas('users', [
            'id' => $passenger->id,
            'credit_score' => 15
        ]);
    }
}
