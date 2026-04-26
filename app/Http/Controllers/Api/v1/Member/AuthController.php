<?php

namespace App\Http\Controllers\Api\v1\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\Business;
use App\Models\BusinessAddress;
use App\Models\BusinessAgreement;
use App\Models\BusinessCategory;
use App\Models\BusinessSubCategory;
use App\Models\BusinessContact;
use App\Models\User;

class AuthController extends Controller
{

    /**
     * Login User
     */
    public function login(Request $request)
    {
        try {
            $key = Str::lower($request->vendor_id) . '|' . $request->ip();

            // Block after 5 attempts
            if (RateLimiter::tooManyAttempts($key, 5)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Too many attempts. Try later.'
                ], 429);
            }

            // Validate all fields INCLUDING captcha
            $validated = $request->validate([
                'vendor_id' => 'required',
                'password' => 'required',
                'captcha' => 'required',
                'captcha_key' => 'required'
            ]);

            // STEP 1: CAPTCHA VALIDATION FIRST
            try {
                $data = decrypt($validated['captcha_key']);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid captcha key'
                ], 422);
            }

            // Expiry check
            if (now()->timestamp - $data['time'] > 120) {
                return response()->json([
                    'status' => false,
                    'message' => 'Captcha expired'
                ], 422);
            }

            // If captcha wrong → STOP here
            if (strtolower($data['code']) !== strtolower($validated['captcha'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid captcha'
                ], 422);
            }

            // STEP 2: Now check login
            $user = User::where('vendor_id', $validated['vendor_id'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {

                RateLimiter::hit($key, 60);

                return response()->json([
                    'status' => false,
                    'message' => 'Invalid credentials',
                    'attempts' => RateLimiter::attempts($key)
                ], 401);
            }

            // Success → clear attempts
            RateLimiter::clear($key);

            // Inactive
            if ($user->status != 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Account inactive'
                ], 403);
            }

            // Token
            $token = $user->createToken('api-token')->plainTextToken;

            $user->load([
                'business.category',
                'business.subCategory',
                'business.address',
                'business.contact',
                'business.agreement',
                'business.kycDetail',
            ]);

            // $user->load([
            //     'business:id,user_id',
            //     'business.category:id,name',
            // ]);

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => new UserResource($user)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     *  Profile
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load([
            'businesses.address',
            'businesses.contact',
            'businesses.agreement'
        ]);

        return response()->json([
            'status' => true,
            'user' => new UserResource($user)
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
