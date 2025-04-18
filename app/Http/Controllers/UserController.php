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
        $users = User::all()->setVisible(['id', 'name', 'nim_nip', 'email']);

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

        return response([
            'id' => $user->id,
            'nim_nip' => $user->nim_nip,
            'name' => $user->name,
            'email' => $user->email,
            'address' => $user->address,
            'phone_number' => $user->phone_number,
            'role_name' => $user->role->role_name,
            'credit_score' => $user->credit_score
        ]);
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
        $passengers = User::all()->where('role_id', 3)->setVisible(['id', 'name', 'nim_nip', 'email']);
    
        return response($passengers);
    }

    public function co()
    {
        $co = User::all()->where('role_id', 2)->setVisible(['id', 'name', 'nim_nip', 'email']);

        return response($co);
    }
}
