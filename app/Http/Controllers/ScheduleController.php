<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AdminCoMiddleware;
use App\Models\Schedule;
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
        $schedules = Schedule::all();

        return response($schedules);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedFields = $request->validate([
            'bus_schedule' => 'required',
            'bus_id' => 'required',
            'route_id' => 'required'
        ]);

        $schedule = Schedule::create($validatedFields);

        return response($schedule, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        return response($schedule);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validatedFields = $request->validate([
            'bus_schedule' => 'required',
            'bus_id' => 'required',
            'route_id' => 'required',
            'closed' => 'required'
        ]);

        $schedule->update($validatedFields);

        return response($schedule);
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
