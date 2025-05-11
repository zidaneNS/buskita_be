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

        $filteredSeats = [];

        foreach ($seats as $seat) {
            $user_name = null;
            if ($seat->user_id !== null) {
                $user_name = $seat->user->name;
            }
            $filteredSeats[] = [
                'id' => $seat->id,
                'user_name' => $user_name,
                'verified' => $seat->verified,
                'seat_number' => $seat->seat_number,
                'user_id' => $seat->user_id,
                'schedule_id' => $schedule->id
            ];
        }

        return response($filteredSeats);
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
            return response(["error" => "seat already taken, schedule closed, or credit score less than 10"], 400);
        }
        
        $request->user()->schedules()->attach($seat->schedule_id);

        $seat->update([
            'user_id' => $request->user()->id
        ]);

        return response([
            'user_name' => $seat->user->name,
            'seat_number' => $seat->seat_number,
            'user_id' => $seat->user_id,
            'schedule_id' => $seat->schedule_id
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Seat $seat)
    {
        $user_name = null;
        if ($seat->user_id !== null) {
            $user_name = $seat->user->name;
        }
        return response([
            'id' => $seat->id,
            'user_name' => $user_name,
            'verified' => $seat->verified,
            'seat_number' => $seat->seat_number,
            'user_id' => $seat->user_id,
            'schedule_id' => $seat->schedule_id
        ]);
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

        if ($new_seat->user_id !== null && $new_seat->user_id !== $user->id) {
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
            'user_name' => $new_seat->user->name,
            'verified' => $new_seat->verified,
            'seat_number' => $seat->seat_number,
            'user_id' => $seat->user_id,
            'schedule_id' => $seat->schedule_id
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
            'user_name' => $seat->user->name,
            'verified' => $seat->verified,
            'seat_number' => $seat->seat_number,
            'user_id' => $seat->user_id,
            'schedule_id' => $seat->schedule_id
        ]);
    }
}
