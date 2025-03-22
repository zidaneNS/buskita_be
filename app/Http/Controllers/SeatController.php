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
    public function index()
    {
        //
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

        $request->user()->schedules()->attach($validatedFields['schedule_id']);

        $seat = Seat::where('bus_id', $validatedFields['bus_id'])->where('row_position', $validatedFields['row_position'])->where('col_position', $validatedFields['col_position'])->where('backseat_position', $validatedFields['backseat_position'])->update([
            'user_id' => $request->user()->id
        ]);

        // dd($seat);

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
