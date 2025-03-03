<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedFields = $request->validate([
            "name" => "required|min:3",
            "email" => "required|email",
            "nim_nip" => "required|min:9",
            "number" => "required",
            "address" => "required",
            "role" => "required",
            "password" => "required|min:8|confirmed",
        ]);

        User::factory()->create($validatedFields);

        return response(["message" => "success"], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response(['error' => 'Invalid credentials'], 400);
        }

        $token = $user->createToken($user->name)->plainTextToken;

        return response(["token" => $token]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response(["message" => "logout success"]);
    }
}
