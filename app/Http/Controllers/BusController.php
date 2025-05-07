<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class BusController extends Controller implements HasMiddleware
{
    public static function middleware(): array
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
        $buses = Bus::all();

        return response($buses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedFields = $request->validate([
            "identity" => "required",
            "available_row" => "required",
            "available_col" => "required",
            "available_backseat" => "required"
        ]);

        if (Bus::where('identity', $validatedFields['identity'])->exists()) {
            return response(["error" => "Bus identity must be unique"], 400);
        }

        $bus = Bus::create($validatedFields)->fresh();

        return response($bus, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Bus $bus)
    {
        // Gate::authorize('admin-co-only');
        return response($bus);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bus $bus)
    {
        $validatedFields = $request->validate([
            "identity" => "required",
            "available_row" => "required",
            "available_col" => "required",
            "available_backseat" => "required"
        ]);

        if (Bus::where('identity', $validatedFields['identity'])->where('id', '!=', $bus->id)->exists()) {
            return response("bus identity must unique", 400);
        }

        $validatedFields['capacity'] = $validatedFields['available_row'] * $validatedFields['available_col'] + $validatedFields['available_backseat'];

        $bus->update($validatedFields);

        return response($bus, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bus $bus)
    {
        $bus->delete();

        return response(null, 204);
    }
}
