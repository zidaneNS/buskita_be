<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AdminCoMiddleware;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ScheduleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('co-co_leader', except: ['index', 'show'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedule::paginate(20);

        $filteredSchedule = [];

        foreach ($schedules as $schedule) {
            $filteredSchedule[] = [
                'id' => $schedule->id,
                'time' => $schedule->time,
                'bus_identity' => $schedule->bus->identity,
                'route_name' => $schedule->route->route_name,
                'closed' => $schedule->closed
            ];
        }

        return response($filteredSchedule);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedFields = $request->validate([
            'time' => 'required',
            'bus_id' => 'required',
            'route_id' => 'required'
        ]);

        $schedule = Schedule::create($validatedFields)->fresh();

        $bus = Bus::find($validatedFields['bus_id']);

        $row = $bus->available_row;
        $col = $bus->available_col;
        $backseat = $bus->available_backseat;

        $total_seats = $row * $col + $backseat;

        for ($i = 1; $i <= $total_seats; $i++) {
            Seat::create([
                'bus_id' => $bus->id,
                'schedule_id' => $schedule->id
            ]);
        }

        return response([
            'id' => $schedule->id,
            'time' => $schedule->time,
            'bus_identity' => $bus->identity,
            'route_name' => $schedule->route->route_name,
            'closed' => $schedule->closed
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        return response([
            'id' => $schedule->id,
            'time' => $schedule->time,
            'bus_identity' => $schedule->bus->identity,
            'route_name' => $schedule->route->route_name,
            'closed' => $schedule->closed
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validatedFields = $request->validate([
            'time' => 'required',
            'bus_id' => 'required',
            'route_id' => 'required',
            'closed' => 'required'
        ]);

        $schedule->update($validatedFields);

        return response([
            'id' => $schedule->id,
            'time' => $schedule->time,
            'bus_identity' => $schedule->bus->identity,
            'route_name' => $schedule->route->route_name,
            'closed' => $schedule->closed
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return response(null, 204);
    }
}
