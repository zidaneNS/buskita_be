<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_co_leader_can_see_all_users(): void
    {
        $co_leader = $this->dummy_co_leader();

        $this->create_co();
        $this->create_passenger();

        $response = $this->actingAs($co_leader)->get('api/users');

        $response
            ->assertStatus(200)
            ->assertJsonCount(10);
    }

    public function test_co_can_see_all_passengers(): void
    {
        $co = $this->dummy_co();

        $this->create_passenger(10);

        $response = $this->actingAs($co)->get('api/passenger');

        $response
            ->assertStatus(200)
            ->assertJsonCount(10);
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

        $response = $this->actingAs($passenger)->get('api/passenger');

        $response->assertStatus(403);
    }

    public function test_co_co_leader_can_see_users_profile(): void
    {
        $co_leader = $this->dummy_co_leader();

        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($co_leader)->get('api/users/' . $passenger->id);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['name']);
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

    public function test_user_can_update_their_profile(): void
    {
        
    }
}
