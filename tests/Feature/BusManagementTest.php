<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BusManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function dummy_co_leader(): User
    {
        $user = User::factory()->create([
            "name" => "zidane",
            "role" => "co_leader",
            "nim_nip" => "181221055"
        ]);

        Sanctum::actingAs($user);
        return $user;
    }

    public function dummy_passenger(): User
    {
        $user = User::factory()->create([
            "name" => "zidane",
            "role" => "passenger",
            "nim_nip" => "181221055"
        ]);

        Sanctum::actingAs($user);
        return $user;
    }

    public function dummy_bus($identity = "8"): Bus
    {
        return Bus::factory()->create([
            "identity" => $identity
        ]);
    }

    public function create_bus(): TestResponse
    {
        return $this->postJson('api/buses', [
            "identity" => "8",
            "available_row" => 7,
            "available_col" => 4,
            "available_backseat" => 5
        ]);
    }

    public function test_admin_co_can_create_bus(): void
    {
        $this->dummy_co_leader();

        $response = $this->create_bus();

        $response->assertStatus(201);
        $this->assertDatabaseHas('buses', [
            "identity" => "8",
            "available_backseat" => 5
        ]);
    }

    public function test_bus_identity_must_unique(): void
    {
        $this->dummy_co_leader();

        $this->dummy_bus();

        $response = $this->create_bus();

        $response->assertStatus(400);
    }

    public function test_admin_co_can_delete_bus(): void
    {
        $this->dummy_co_leader();

        $bus = $this->dummy_bus();

        $response = $this->deleteJson('api/buses/' . $bus->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('buses', [
            "id" => $bus->id
        ]);
    }

    public function test_admin_co_can_update(): void
    {
        $this->dummy_co_leader();

        $bus = $this->dummy_bus();

        $response = $this->putJson('api/buses/' . $bus->id, [
            "identity" => "8",
            "available_backseat" => 6,
            "available_row" => $bus->available_row,
            "available_col" => $bus->available_col
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('buses', [
            "id" => $bus->id,
            "identity" => "8",
            "available_backseat" => 6
        ]);
    }

    public function test_bus_identity_unique_when_update(): void
    {
        $this->dummy_co_leader();

        $bus1 = $this->dummy_bus();
        $bus2 = $this->dummy_bus("2");

        $response = $this->putJson('api/buses/' . $bus2->id, [
            "identity" => $bus1->identity,
            "available_col" => $bus2->available_col,
            "available_row" => $bus2->available_row,
            "available_backseat" => $bus2->available_backseat
        ]);

        $response->assertStatus(400);
    }

    public function test_admin_co_can_get_all_buses(): void
    {
        $this->dummy_co_leader();

        $this->dummy_bus();
        $this->dummy_bus("2");

        $response = $this->getJson('api/buses');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_admin_co_can_get_bus_by_id(): void
    {
        $this->dummy_co_leader();

        $bus = $this->dummy_bus();

        $response = $this->get('api/buses/' . $bus->id);

        $response->assertStatus(200);
        $response->assertJson([
            "id" => $bus->id
        ]);
    }

    public function test_user_cannot_access_bus(): void
    {
        $this->dummy_passenger();

        $bus = $this->dummy_bus();

        $response = $this->get('api/buses/' . $bus->id);

        $response->assertStatus(403);
    }
}
