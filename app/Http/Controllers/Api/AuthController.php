<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $userRole = Role::firstOrCreate(['name' => 'USER']);
        $user->roles()->syncWithoutDetaching([$userRole->id]);

        $abilities = $user->hasRole('ADMIN')
            ? ['*']
            : ['order:create', 'order:view'];

        $token = $user->createToken(
            $request->input('device_name', 'api-token'),
            $abilities
        )->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Login an existing user.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = $request->user();

        $abilities = $user->hasRole('ADMIN')
            ? ['*']
            : ['order:create', 'order:view'];

        $token = $user->createToken(
            $request->device_name,
            $abilities
        )->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Get authenticated user profile.
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Logged out'
        ], 200);
    }
}
