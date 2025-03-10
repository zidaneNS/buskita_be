<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class ScheduleController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum')
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
        if (! Gate::allows('co_access')) {
            return response(null, 403);
        }
        
        $validatedFields = $request->validate([
            "bus_schedule" => "required",
            "capacity" => "required",
            "number" => "required"
        ]);
        
        $schedule = Schedule::factory()->create($validatedFields);
        
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
        if (! Gate::allows('co_access')) {
            return response(null, 403);
        }
        
        $validatedFields = $request->validate([
            "bus_schedule" => "required",
            "capacity" => "required",
            "number" => "required"
        ]);

        $schedule->update($validatedFields);

        return response(["message" => "update success"], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        if (! Gate::allows('co_access')) {
            return response(null, 403);
        }

        $schedule->delete();

        return response(null, 204);
    }
}
