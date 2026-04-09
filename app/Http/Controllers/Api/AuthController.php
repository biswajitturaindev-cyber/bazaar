<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KycDetailResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;

use App\Models\Business;
use App\Models\BusinessAddress;
use App\Models\BusinessAgreement;
use App\Models\BusinessContact;
use App\Models\User;

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|digits_between:10,15|unique:users,mobile',
            'password' => 'required|min:6',

            // Business
            'business_name' => 'required|string|max:255',
            'business_category_id' => 'required|numeric',
            'business_sub_category_id' => 'required|numeric',

            // Contact
            'contact_person_name' => 'required|string|max:255',
            'contact_number' => 'required|digits_between:10,15',

            // Agreement
            'agree_terms' => 'required|accepted',
            'confirm_info' => 'required|accepted',

            // Optional
            'sponsor_id' => 'nullable|numeric',
            'dob' => 'nullable|date',
            'gender' => 'nullable|numeric',
            'years_in_business' => 'nullable|numeric',
            'gst_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:20',
            'fssai_license' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|digits:6',
            'landmark' => 'nullable|string|max:255',
            'google_map_location' => 'nullable|string',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            // Hash password
            $data['password'] = Hash::make($data['password']);

            // Create User
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'dob' => $data['dob'] ?? null,
                'gender' => $data['gender'] ?? null,
                'password' => $data['password'],
                'status' => 1,
            ]);

            // Generate Vendor ID
            $randomNumber = ($user->id * 7919) % 100000000;
            $vendorId = 'RV' . str_pad($randomNumber, 8, '0', STR_PAD_LEFT);
            $user->update(['vendor_id' => $vendorId]);

            // Create Business
            $business = Business::create([
                'user_id' => $user->id,
                'sponsor_id' => $data['sponsor_id'] ?? null,
                'business_name' => $data['business_name'],
                'business_category_id' => $data['business_category_id'],
                'business_sub_category_id' => $data['business_sub_category_id'],
                'years_in_business' => $data['years_in_business'] ?? null,
                'gst_number' => $data['gst_number'] ?? null,
                'pan_number' => $data['pan_number'] ?? null,
                'fssai_license' => $data['fssai_license'] ?? null,
                'registration_number' => $data['registration_number'] ?? null,
            ]);

            // Business Address
            BusinessAddress::create([
                'business_id' => $business->id,
                'address_line_1' => $data['address_line_1'] ?? null,
                'address_line_2' => $data['address_line_2'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'pincode' => $data['pincode'] ?? null,
                'landmark' => $data['landmark'] ?? null,
                'google_map_location' => $data['google_map_location'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
            ]);

            // Business Contact
            BusinessContact::create([
                'business_id' => $business->id,
                'contact_person_name' => $data['contact_person_name'],
                'contact_number' => $data['contact_number'],
            ]);

            // Business Agreement
            BusinessAgreement::create([
                'business_id' => $business->id,
                'agree_terms' => 1,
                'confirm_info' => 1,
            ]);

            DB::commit();

            // Load relations
            $user->load([
                'business.category',
                'business.subCategory',
                'business.address',
                'business.contact',
                'business.agreement',
                'business.bankDetail',
                'business.kycDetail',
                'business.operationalDetail'
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Registration successful',
                'user' => new UserResource($user),
                'vendor_id' => $vendorId
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Vendor Register Error: ' . $e->getMessage() . ' | Line: ' . $e->getLine());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine()
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
                'vendor_id' => 'required',
                'password' => 'required'
            ]);

            // Find user
            $user = User::where('vendor_id', $validated['vendor_id'])->first();

            // Invalid credentials
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid vendor id or password'
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
                'business.agreement',
                //'business.bankDetail',
                'business.kycDetail',
                //'business.operationalDetail'
            ]);

            // Success response
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                //'user' => new UserResource($user)
                'data' => [
                    'user' => new UserResource($user),
                    'kycDetail' => new KycDetailResource($user->business->kycDetail)
                ]
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
