<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            "nim_nip" => "required",
            "password" => "required"
        ]);

        $user = User::where('nim_nip', $request->nim_nip)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response(['error' => 'Invalid credentials'], 400);
        }

        $token = $user->createToken($user->name)->plainTextToken;

        return response(["token" => $token]);
    }

    public function register(Request $request)
    {
        $credentials = $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed",
            "address" => "required",
            "phone_number" => "required|unique:users",
            "role_id" => "required",
            "nim_nip" => "required"
        ]);

        $user = User::create($credentials)->fresh();

        return response($user, 201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response(null, 204);
    }
}
