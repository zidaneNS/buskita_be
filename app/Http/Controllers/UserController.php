<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('co_leader', only: ['index']),
            new Middleware('co-co_leader', only: ['co', 'passenger'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::whereNot('role_id', 1)->get();

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
    public function show(User $user)
    {
        Gate::authorize('view', $user);

        return response($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    public function passenger()
    {
        $passengers = User::where('role_id', 3)->get();
    
        return response($passengers);
    }

    public function co()
    {
        $co = User::where('role_id', 2)->get();

        return response($co);
    }
}
