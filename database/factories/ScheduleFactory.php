<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "ikut_berangkat" => 0,
            "ikut_pulang" => 0,
            "berangkat" => 0,
            "pulang" => 0,
            "pp" => 0,
            "pp_khusus" => 0
        ];
    }
}
