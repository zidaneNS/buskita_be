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
    public function index(Request $request, Schedule $schedule)
    {
        $seats = $schedule->seats;

        return response($seats);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedFields = $request->validate([
            'seat_id' => 'required'
        ]);

        $seat = Seat::find($validatedFields['seat_id']);

        $has_another_seat = Seat::where('schedule_id', $seat->schedule_id)
            ->where('user_id', $request->user()->id)
            ->where('id', '!=', $seat->id)
            ->exists();
        
        if ($request->user()->credit_score < 10 || 
            $seat === null ||
            $seat->user_id !== null ||
            $seat->schedule->closed === true ||
            $has_another_seat) {
            return response(["message" => "seat already taken, schedule closed, or credit score less than 10"], 400);
        }
        
        $request->user()->schedules()->attach($seat->schedule_id);

        $seat->update([
            'user_id' => $request->user()->id
        ]);

        return response([
            'user_id' => $seat->user_id,
            'seat_number' => $seat->seat_number
        ]);
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
            'new_seat_id' => 'required'
        ]);

        $user = $request->user();
        $new_seat = Seat::find($validatedFields['new_seat_id']);

        if ($seat->user_id !== null && $seat->user_id !== $user->id && $new_seat->id === $seat->id) {
            return response(['message' => 'already occupied'], 400);
        }

        $seat->update([
            'user_id' => null
        ]);

        
        $new_seat->update([
            'user_id' => $request->user()->id
        ]);

        return response([
            'id' => $new_seat->id,
            'user_id' => $new_seat->user_id,
            'verified' => $new_seat->verified,
            'seat_number' => $seat->seat_number
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seat $seat)
    {
        Gate::authorize('delete', $seat);

        $user = $seat->user;

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

        return response([
            'id' => $seat->id,
            'user_id' => $seat->user_id,
            'verified' => $seat->verified,
            'seat_number' => $seat->seat_number
        ]);
    }
}
