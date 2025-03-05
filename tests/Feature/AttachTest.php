<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AttachTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_user_can_apply_list() {
        $this->dummy_user();
    }
}
