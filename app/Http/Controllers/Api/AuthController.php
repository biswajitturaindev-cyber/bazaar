<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Business;
use App\Models\BusinessAddress;
use App\Models\BusinessAgreement;
use App\Models\BusinessContact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register New User + Business Setup
     */
    public function register(Request $request)
    {
        // Validation
        $data = $request->validate([
            // User
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|unique:users',
            'password' => 'required|min:6',

            // Business
            'business_name' => 'required',
            'business_category_id' => 'required',
            'business_sub_category_id' => 'required',

            // Contact
            'contact_person_name' => 'required',
            'contact_number' => 'required',

            // Agreement
            'agree_terms' => 'required',
            'confirm_info' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // Hash password
            $data['password'] = Hash::make($data['password']);

            //👤 Create User
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'mobile' => $request->mobile,
                'dob' => $request->dob,
                'gender' => $request->gender,
                'status' => 1
            ]);

            // Generate Vendor ID
            $randomNumber = ($user->id * 7919) % 100000000;
            $vendorId = 'RV' . str_pad($randomNumber, 8, '0', STR_PAD_LEFT);

            // Save Vendor ID
            $user->update([
                'vendor_id' => $vendorId
            ]);

            // Create Business
            $business = Business::create([
                'user_id' => $user->id,
                'sponsor_id' => $request->sponsor_id,
                'business_name' => $request->business_name,
                'business_category_id' => $request->business_category_id,
                'business_sub_category_id' => $request->business_sub_category_id,
                'years_in_business' => $request->years_in_business,
                'gst_number' => $request->gst_number,
                'pan_number' => $request->pan_number,
                'fssai_license' => $request->fssai_license,
                'registration_number' => $request->registration_number,
            ]);

            // Address
            BusinessAddress::create([
                'business_id' => $business->id,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'landmark' => $request->landmark,
                'google_map_location' => $request->google_map_location,
            ]);

            // Contact
            BusinessContact::create([
                'business_id' => $business->id,
                'contact_person_name' => $request->contact_person_name,
                'contact_number' => $request->contact_number,
            ]);

            // Agreement
            BusinessAgreement::create([
                'business_id' => $business->id,
                'agree_terms' => $request->agree_terms,
                'confirm_info' => $request->confirm_info,
            ]);

            // Generate Token
            $token = $user->createToken('api-token')->plainTextToken;

            DB::commit();

            // Load relations (avoid N+1)
            $user->load([
               'business.category',
               'business.subCategory',
               'business.address',
               'business.contact',
               'business.agreement'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Registration successful',
                'token' => $token,
                'user' => new UserResource($user)
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login User
     */
    public function login(Request $request)
    {
        try {

            // Validate request
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Find user
            $user = User::where('email', $validated['email'])->first();

            // Invalid credentials
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid email or password'
                ], 401);
            }

            // Check active status
            if ($user->status != 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Account inactive'
                ], 403);
            }

            // Create token
            $token = $user->createToken('api-token')->plainTextToken;

            // Load relations
            $user->load([
                'business.category',
                'business.subCategory',
                'business.address',
                'business.contact',
                'business.agreement'
            ]);

            // Success response
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => new UserResource($user)
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            // Validation errors
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            // General errors
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() // remove in production if needed
            ], 500);
        }
    }
    /**
     * 👤 Profile
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
