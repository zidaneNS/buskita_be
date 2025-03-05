<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return response($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $user)
    {
        if ($request->user()->id !== $user->id && $request->user()->role !== "co") {
            return response(null, 403);
        } else {
            return response($user);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validatedFields = $request->validate([
            "name" => "required|min:3",
            "email" => "required|email",
            "nim_nip" => "required|min:9",
            "number" => "required",
            "address" => "required"
        ]);

        $user->update($validatedFields);

        return response(["message" => "update success"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
