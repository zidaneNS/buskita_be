<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ScheduleManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function dummy_co_leader(): User
    {
        $user = User::factory()->create([
            'name' => 'zidane',
            'nim_nip' => '181221055'
        ]);

        Sanctum::actingAs($user);

        return $user;
    }

    public function dummy_passenger(): User
    {
        $user = User::factory()->create([
            'name' => 'zidane',
            'nim_nip' => '181221055',
            'role_id' => 3
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

    public function dummy_schedule($bus_id, $route_id = 1): Schedule
    {
        return Schedule::create([
            'time' => now()->format('Y-m-d H:i:s'),
            'bus_id' => $bus_id,
            'route_id' => $route_id
        ]);
    }

    public function test_co_co_leader_can_create_schedule(): void
    {
        $this->dummy_co_leader();

        $bus = $this->dummy_bus();

        $response = $this->postJson('api/schedules', [
            "time" => now()->format('Y-m-d H:i:s'),
            "bus_id" => $bus->id,
            "route_id" => 1
        ]);

        $response
        ->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'time',
                'bus_identity',
                'route_name',
                'closed'
            ]);
        $this->assertDatabaseHas('schedules', [
            "bus_id" => $bus->id
        ]);
    }

    public function test_co_co_leader_can_update_schedule(): void
    {
        $this->dummy_co_leader();

        $bus = $this->dummy_bus();

        $schedule = $this->dummy_schedule($bus->id);

        $response = $this->putJson('api/schedules/' . $schedule->id, [
            "time" => "2026-04-21 08:00:00",
            "bus_id" => $bus->id,
            "route_id" => $schedule->route_id,
            "closed" => false
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'time',
                'bus_identity',
                'route_name',
                'closed'
            ]);
        $this->assertDatabaseHas('schedules', [
            "id" => $schedule->id,
            "time" => "2026-04-21 08:00:00"
        ]);
    }

    public function test_co_co_leader_can_delete_schedule(): void
    {
        $this->dummy_co_leader();

        $bus = $this->dummy_bus();

        $schedule = $this->dummy_schedule($bus->id);

        $response = $this->delete('api/schedules/' . $schedule->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('schedules', [
            'id' => $schedule->id
        ]);
    }

    public function test_user_can_get_all_schedules(): void
    {
        $this->dummy_passenger();

        $bus1 = $this->dummy_bus();
        $bus2 = $this->dummy_bus('4');

        $this->dummy_schedule($bus1->id);
        $this->dummy_schedule($bus2->id);

        $response = $this->get('api/schedules');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'time',
                    'bus_identity',
                    'route_name',
                    'closed'
                ]
            ]);
        $response->assertJsonCount(5);
    }

    public function test_user_can_get_schedule_by_id(): void
    {
        $this->dummy_passenger();

        $bus = $this->dummy_bus();

        $schedule = $this->dummy_schedule($bus->id);

        $response = $this->get('api/schedules/' . $schedule->id);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'time',
                'bus_identity',
                'route_name',
                'closed'
            ])
            ->assertJson([
                "id" => $schedule->id,
                "bus_identity" => $bus->identity
            ]);
    }

    public function test_passenger_cant_access_create_update_delete(): void
    {
        $this->dummy_passenger();

        $bus = $this->dummy_bus();

        $schedule = $this->dummy_schedule($bus->id);

        $response1 = $this->postJson('api/schedules', [
            "time" => now()->format('Y-m-d H:i:s'),
            "bus_id" => $bus->id,
            "route_id" => 1
        ]);
        $response2 = $this->putJson('api/schedules/' . $schedule->id);
        $response3 = $this->delete('api/schedules/' . $schedule->id);

        $response1->assertStatus(403);
        $response2->assertStatus(403);
        $response3->assertStatus(403);
    }

    public function test_schedule_deleted_1_hour_after_the_date(): void
    {
        $this->dummy_passenger();

        $bus = $this->dummy_bus();

        $dt = Carbon::parse(now())->subHour();

        $schedule = Schedule::create([
            'time' => $dt,
            'bus_id' => $bus->id,
            'route_id' => 1
        ]);

        $this->get('api/schedules');

        $this->assertDatabaseMissing('schedules', [
            'id' => $schedule->id
        ]);
    }

    public function test_can_get_all_schedules_by_route_id(): void
    {
        $passenger = $this->dummy_passenger();

        $bus1 = $this->dummy_bus();
        $bus2 = $this->dummy_bus('4');

        $this->dummy_schedule($bus1->id, 2);
        $this->dummy_schedule($bus2->id, 2);

        $response = $this->actingAs($passenger)->get('api/schedules/route/2');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'time',
                    'bus_identity',
                    'route_name',
                    'closed'
                ]
            ]);
        $response->assertJsonCount(2);
    }

    public function test_can_get_all_routes(): void
    {
        $passenger = $this->dummy_passenger();

        $response = $this->actingAs($passenger)->get('api/routes');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'route_name'
                ]
            ])
            ->assertJsonCount(2);
    }
}
