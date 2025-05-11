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
            new Middleware('co_leader', only: ['index', 'store']),
            new Middleware('co-co_leader', only: ['co', 'passenger'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all()->setVisible(['id', 'name', 'nim_nip', 'email', 'role_id']);

        $filteredUsers = [];

        foreach ($users as $user) {
            $filteredUsers[] = [
                'id' => $user->id,
                'name' => $user->name,
                'nim_nip' => $user->nim_nip,
                'email' => $user->email,
                'role_name' => $user->role->role_name,
                'phone_number' => $user->phone_number,
                'address' => $user->address,
                'credit_score' => $user->credit_score
            ];
        }

        return response($filteredUsers);
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed",
            "address" => "required",
            "phone_number" => "required|unique:users",
            "nim_nip" => "required"
        ]);

    $credentials['role_id'] = 2;

        $user = User::create($credentials)->fresh();

        return response([
            'id' => $user->id,
            'nim_nip' => $user->nim_nip,
            'name' => $user->name,
            'email' => $user->email,
            'address' => $user->address,
            'phone_number' => $user->phone_number,
            'role_name' => $user->role->role_name,
            'credit_score' => $user->credit_score
        ], 201);
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
        Gate::authorize('modify', $user);

        $validatedFields = $request->validate([
            'nim_nip' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'phone_number' => 'required'
        ]);

        $user->update($validatedFields);

        $user->refresh();

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
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('modify', $user);

        $user->delete();

        return response(null, 204);
    }

    public function passenger()
    {
        $passengers = User::all()->where('role_id', 3);
    
        $filteredPassengers = [];

        foreach ($passengers as $passenger) {
            $filteredPassengers[] = [
                'id' => $passenger->id,
                'name' => $passenger->name,
                'nim_nip' => $passenger->nim_nip,
                'email' => $passenger->email,
                'role_name' => $passenger->role->role_name,
                'address' => $passenger->address,
                'phone_number' => $passenger->phone_number,
                'credit_score' => $passenger->credit_score
            ];
        }

        return response($filteredPassengers);
    }

    public function co()
    {
        $cos = User::all()->where('role_id', 2);

        $filteredCos = [];

        foreach ($cos as $co) {
            $filteredCos[] = [
                'id' => $co->id,
                'name' => $co->name,
                'nim_nip' => $co->nim_nip,
                'email' => $co->email,
                'role_name' => $co->role->role_name,
                'phone_number' => $co->phone_number,
                'address' => $co->address,
                'credit_score' => $co->credit_score
            ];
        }

        return response($filteredCos);
    }

    public function getData(Request $request)
    {
        $user = $request->user();

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
}
