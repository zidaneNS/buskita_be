<?php

namespace Database\Seeders;

use App\Models\Route;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Route::create([
            'route_name' => 'GSK-SBY'
        ]);

        Route::create([
            'route_name' => 'SBY-GSK'
        ]);
    }
}
