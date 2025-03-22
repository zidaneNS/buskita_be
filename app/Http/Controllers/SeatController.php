<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Seat;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validatedFields = $request->validate([
            'schedule_id' => 'required'
        ]);

        $schedule = Schedule::find($validatedFields['schedule_id']);

        $seats = Seat::where('bus_id', $schedule->bus_id)->get();

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

        if ($request->user()->credit_score < 10 || Seat::where('bus_id', $validatedFields['bus_id'])
        ->where('row_position', $validatedFields['row_position'])
        ->where('col_position', $validatedFields['col_position'])
        ->where('backseat_position', $validatedFields['backseat_position'])
        ->pluck('user_id')[0] !== null) {
            return response(["message" => "seat already taken or credit score less than 10"], 400);
        }
        
        $request->user()->schedules()->attach($validatedFields['schedule_id']);
        
        $seat = Seat::where('bus_id', $validatedFields['bus_id'])
            ->where('row_position', $validatedFields['row_position'])
            ->where('col_position', $validatedFields['col_position'])
            ->where('backseat_position', $validatedFields['backseat_position'])
            ->update([
            'user_id' => $request->user()->id
        ]);

        return response($seat);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seat $seat)
    {
        //
    }
}
