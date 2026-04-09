<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

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
        try {
    
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required|digits_between:10,15|unique:users,mobile',
                'password' => 'required|min:6',
    
                'business_name' => 'required|string|max:255',
                'business_category_id' => 'required|numeric',
                'business_sub_category_id' => 'required|numeric',
    
                'agree_terms' => 'required|accepted',
                'confirm_info' => 'required|accepted',
    
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
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'type' => 'validation_error',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $data = $validator->validated();
    
            DB::beginTransaction();
    
            // Hash password
            $data['password'] = Hash::make($data['password']);
    
            // Create User
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'mobile' => $data['mobile'],
                'dob' => $data['dob'] ?? null,
                'gender' => $data['gender'] ?? null,
                'status' => 1
            ]);
    
            // Vendor ID
            $randomNumber = ($user->id * 7919) % 100000000;
            $vendorId = 'RV' . str_pad($randomNumber, 8, '0', STR_PAD_LEFT);
            $user->update(['vendor_id' => $vendorId]);
    
            // Business
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
    
            // Address
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
    
            // Contact
            BusinessContact::create([
                'business_id' => $business->id,
                'contact_person_name' => $data['name'] ?? null,
                'contact_number' => $data['mobile'] ?? null,
            ]);
    
            // Agreement
            BusinessAgreement::create([
                'business_id' => $business->id,
                'agree_terms' => 1,
                'confirm_info' => 1,
            ]);
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'message' => 'Registration successful with VendorID: '.$vendorId,
                'user_id' => $user->id,
                'vendor_id' => $vendorId
            ], 201);
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
    
            Log::error('Vendor Register Error: ' . $e->getMessage() . ' | Line: ' . $e->getLine());
    
            return response()->json([
                'status' => false,
                'type' => 'server_error',
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
