<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function dummy_user(): User
    {
        $user = User::factory()->create([
            "nim_nip" => "181221055"
        ]);

        Sanctum::actingAs($user);

        return $user;
    }

    public function dummy_co(): User
    {
        $user = User::factory()->create([
            "nim_nip" => "181221003",
            "role" => "co"
        ]);

        Sanctum::actingAs($user);
        return $user;
    }

    public function test_user_can_update_profile() {
        $user = $this->dummy_user();

        $response = $this->putJson('api/user/' . $user->id, [
            "nim_nip" => "181221055",
            "name" => "zidane",
            "role" => "user",
            "password" => "password",
            "address" => fake()->address(),
            "credit_score" => 100,
            "email" => fake()->safeEmail(),
            "number" => fake()->phoneNumber()
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            "nim_nip" => "181221055",
            "name" => "zidane"
        ]);
    }

    public function test_only_user_can_see_their_profile_self() {
        $user = $this->dummy_user();

        $response = $this->get('api/user/' . $user->id);

        $other_user = User::factory()->create([
            "nim_nip" => "181221045"
        ]);
        
        $response_error = $this->get('api/user/' . $other_user->id);
        
        $response_error->dump();

        $response->assertStatus(200);
        $response_error->assertStatus(403);
    }

    public function test_co_can_see_user_profile() {
        $user = $this->dummy_co();

        $response = $this->get('api/user/' . $user->id);

        $response->assertStatus(200);
    }

    public function test_can_get_all_user_profile() {
        $user = $this->dummy_user();

        $response = $this->get('api/user');

        $response->assertStatus(200);
    }
}
