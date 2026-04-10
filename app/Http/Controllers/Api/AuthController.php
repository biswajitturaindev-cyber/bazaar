<?php

namespace App\Http\Controllers\Api;

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
     * Register New User + Business Setup
     */
    
    public function register(Request $request)
    {
        $rules = [
            
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile' => 'required|digits:10',
            'password' => 'required|min:6',
    
            'business_name' => 'required|string|max:255',
            'business_category_id' => 'required|string|max:100',
            'business_sub_category_id' => 'required|string|max:100',
    
            'contact_person_name' => 'required|string|max:255',
            'contact_number' => 'required|digits:10',
    
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
        ];
    
        $messages = [
            'name.required' => 'Full name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Enter a valid email address',
    
            'mobile.required' => 'Mobile number is required',
            'mobile.digits' => 'Mobile must be 10 digits',
    
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
    
            'business_name.required' => 'Business name is required',
            'business_category_id.required' => 'Please select a business category',
            'business_sub_category_id.required' => 'Please select a subcategory',
    
            'contact_person_name.required' => 'Contact person name is required',
            'contact_number.digits' => 'Contact number must be 10 digits',
            'contact_number.required' => 'Contact number is required',
    
            'agree_terms.accepted' => 'You must accept terms & conditions',
            'confirm_info.accepted' => 'You must confirm the information',
        ];
    
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $data = $validator->validated();
    
        DB::beginTransaction();
    
        try {
            
            $catiddecoded = Hashids::decode($request->business_category_id);
            $subcatiddecoded = Hashids::decode($request->business_sub_category_id);
            
            if (empty($catiddecoded) || empty($subcatiddecoded)) {
                
                throw new \Exception('Invalid category or subcategory');
            }
            if (!BusinessCategory::where('id', $catiddecoded[0])->exists()) {
                throw new \Exception('Invalid business category');
            }
            
            if (!BusinessSubCategory::where('id', $subcatiddecoded[0])->exists()) {
                throw new \Exception('Invalid business subcategory');
            }
            
            $data['business_category_id'] = $catiddecoded[0];
            $data['business_sub_category_id'] = $subcatiddecoded[0];
            
            $psw = $data['password'];
            
            $data['password'] = Hash::make($data['password']);
    
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'dob' => $data['dob'] ?? null,
                'gender' => $data['gender'] ?? null,
                'password' => $data['password'],
                'status' => 1,
            ]);
    
            $randomNumber = ($user->id * 7919) % 100000000;
            $vendorId = 'RV' . str_pad($randomNumber, 8, '0', STR_PAD_LEFT);
            $user->update(['vendor_id' => $vendorId]);
    
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
    
            BusinessContact::create([
                'business_id' => $business->id,
                'contact_person_name' => $data['contact_person_name'],
                'contact_number' => $data['contact_number'],
            ]);
    
            BusinessAgreement::create([
                'business_id' => $business->id,
                'agree_terms' => 1,
                'confirm_info' => 1,
            ]);
    
            DB::commit();
    
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
            
            $payload = [
                'business_name' => $data['business_name'],
                'vendor_name' => $data['name'],
                'vendor_id' => $vendorId,
                'psw' => $psw,
            ];
            
            return response()->json([
                'status' => true,
                'message' => 'Registration successful',
                'user' => $payload
            ], 201);
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            Log::error('Vendor Register Error: ' . $e->getMessage());
        
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

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
