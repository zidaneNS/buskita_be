<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Schedule;
use App\Models\Seat;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bus = Bus::factory()->create([
            'available_row' => 6,
            'available_col' => 4,
            'available_backseat' => 6,
            'identity' => '12'
        ]);

        $dates = ['2025-03-13', '2025-03-14', '2025-03-15'];

        foreach ($dates as $date) {
            $schedule = Schedule::factory()->create([
                'time' => Carbon::parse($date),
                'bus_id' => $bus->id,
                'route_id' => 1,
                'closed' => false
            ]);

            $total_seats = $bus->available_row * $bus->available_col + $bus->available_backseat;

            for ($i = 1; $i <= $total_seats; $i++) {
                Seat::create([
                    'bus_id' => $bus->id,
                    'schedule_id' => $schedule->id,
                    'seat_number' => $i,
                    'verified' => false
                ]);
            }
        }
    }
}
