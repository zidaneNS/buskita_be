<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class SeatController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('co-co_leader', only: ['verify'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validatedFields = $request->validate([
            'schedule_id' => 'required'
        ]);

        $schedule = Schedule::find($validatedFields['schedule_id']);

        $seats = $schedule->seats;

        return response($seats);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedFields = $request->validate([
            'bus_id' => 'required',
            'row_position' => 'required',
            'col_position' => 'required',
            'backseat_position' => 'required',
            'schedule_id' => 'required'
        ]);

        
        if ($request->user()->credit_score < 10 || 
            Seat::where('bus_id', $validatedFields['bus_id'])
            ->where('row_position', $validatedFields['row_position'])
            ->where('col_position', $validatedFields['col_position'])
            ->where('backseat_position', $validatedFields['backseat_position'])
            ->where('schedule_id', $validatedFields['schedule_id'])
            ->pluck('user_id')[0] !== null ||
            Schedule::whereRelation('users', 'users.id', $request->user()->id)->exists()) {
            return response(["message" => "seat already taken or credit score less than 10"], 400);
        }
        
        $request->user()->schedules()->attach($validatedFields['schedule_id']);
        
        $seat = Seat::where('bus_id', $validatedFields['bus_id'])
        ->where('row_position', $validatedFields['row_position'])
        ->where('col_position', $validatedFields['col_position'])
        ->where('backseat_position', $validatedFields['backseat_position'])
        ->where('schedule_id', $validatedFields['schedule_id'])
        ->get();

        $seat->toQuery()->update([
            'user_id' => $request->user()->id
        ]);

        return response($seat[0]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Seat $seat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Seat $seat)
    {
        $validatedFields = $request->validate([
            'schedule_id' => 'required',
            'row_position' => 'required',
            'col_position' => 'required',
            'backseat_position' => 'required'
        ]);

        $seat->update([
            'user_id' => null
        ]);

        $newSeat = Seat::where('schedule_id', $validatedFields['schedule_id'])
            ->where('row_position', $validatedFields['row_position'])
            ->where('col_position', $validatedFields['col_position'])
            ->where('backseat_position', $validatedFields['backseat_position'])
            ->get();
        $newSeat->toQuery()->update([
            'user_id' => $request->user()->id
        ]);

        return response($newSeat[0]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seat $seat)
    {
        Gate::authorize('delete', $seat);

        $user = User::find($seat->user_id);

        $user->schedules()->detach($seat->schedule_id);

        $seat->update([
            'user_id' => null
        ]);

        return response(null, 204);
    }

    public function verify(Seat $seat)
    {
        $seat->update([
            'verified' => !$seat->verified
        ]);

        return response($seat);
    }
}
