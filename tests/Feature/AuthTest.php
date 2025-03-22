<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    public function dummy_co_leader(): User
    {
        $user = User::factory()->create([
            "name" => "zidane",
            "nim_nip" => "181221055"
        ]);

        Sanctum::actingAs($user);
        return $user;
    }

    public function register(): TestResponse
    {
        return $this->postJson('api/register', [
            "name" => "zidane",
            "nim_nip" => "181221055",
            "address" => fake()->address(),
            "phone_number" => fake()->phoneNumber(),
            "email" => "zidane@gmail.com",
            "password" => "password",
            "password_confirmation" => "password",
            "role_id" => 1
        ]);
    }

    public function test_can_register(): void
    {
        $response = $this->register();

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            "name" => "zidane",
            "nim_nip" => "181221055",
            "credit_score" => 15
        ]);
    }

    public function test_can_login(): void
    {
        $this->register();

        $response = $this->postJson('api/login', [
            "email" => "zidane@gmail.com",
            "password" => "password"
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    public function test_can_logout(): void
    {
        $this->dummy_co_leader();

        $response = $this->get('api/logout');
        $response->assertStatus(204);
    }
}
