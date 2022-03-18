<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // register user
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        // check if user exists
        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response()->json([
                'message' => 'User already exists'
            ], 409);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user-name' => $user->name,
            'user-email' => $user->email,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // login user
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user-name' => $user->name,
            'user-email' => $user->email,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // get user
    public function me(Request $request)
    {
        return response()->json(
            $request->user()
        );
    }

    // logout user
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
