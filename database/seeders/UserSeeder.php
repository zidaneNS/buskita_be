<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'dummyCoLeader',
            'nim_nip' => '11111111'
        ]);
        User::factory()->create([
            'name' => 'dummyCo',
            'nim_nip' => '22222222',
            'role_id' => 2
        ]);
        User::factory()->create([
            'name' => 'dummyPassenger',
            'nim_nip' => '33333333',
            'role_id' => 3
        ]);
    }
}
