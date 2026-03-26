<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     *   Register New User
     * - Validate input
     * - Hash password
     * - Create user
     * - Generate Sanctum token
     */
    public function register(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        // Hash password before saving
        $data['password'] = Hash::make($data['password']);

        // Create user
        $user = User::create($data);

        // Generate API token (Sanctum)
        $token = $user->createToken('api-token')->plainTextToken;

        // Return response
        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     *   Login User
     * - Check credentials
     * - Generate token
     */
    public function login(Request $request)
    {
        // Validate request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 🔍 Find user by email
        $user = User::where('email', $request->email)->first();

        // Invalid credentials check
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // 🔑 Create new token
        $token = $user->createToken('api-token')->plainTextToken;

        // Return response
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     *  Get Logged-in User Profile
     * - Requires Sanctum authentication
     */
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => $request->user()
        ]);
    }

    /**
     * 🚪 Logout User
     * - Delete current access token
     */
    public function logout(Request $request)
    {
        // Delete current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
