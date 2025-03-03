<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function register()
    {
        return $this->postJson('api/register', [
            "name" => "zidane",
            "email" => "zidane@gmail.com",
            "nim_nip" => "181221055",
            "number" => "083831139680",
            "role" => "admin",
            "password" => "zidane123",
            "password_confirmation" => "zidane123",
            "address" => fake()->address()
        ]);
    }

    public function test_can_register(): void
    {
        $response = $this->register();

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            "name" => "zidane",
            "nim_nip" => "181221055"
        ]);
    }

    public function login()
    {
        return $this->postJson('api/login', [
            "email" => "zidane@gmail.com",
            "password" => "zidane123"
        ]);
    }

    public function test_can_login(): void
    {
        $this->register();
        $response = $this->login();

        $response->assertStatus(200);
        $response->assertJsonStructure(["token"]);
    }

    public function test_can_logout(): void
    {
        $user = User::factory()->create([
            "nim_nip" => "181221055"
        ]);

        Sanctum::actingAs($user);

        $response = $this->get('api/logout');
        $response->assertStatus(200);
        $response->assertJson(["message" => "logout success"]);
    }
}
