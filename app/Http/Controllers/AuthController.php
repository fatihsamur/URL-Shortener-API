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
            'userName' => $user->name,
            'userEmail' => $user->email,
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
            'userName' => $user->name,
            'userEmail' => $user->email,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // get user
    public function me(Request $request)
    {
        $user = $request->user();
        if ($user) {
            return $user;
        } else {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
    }

    // logout user
    public function logOut(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                'message' => 'Logged out',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
    }
}
