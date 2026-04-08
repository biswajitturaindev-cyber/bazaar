<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\UserResource;
use App\Models\Business;
use App\Models\BusinessAddress;
use App\Models\BusinessAgreement;
use App\Models\BusinessCategory;
use App\Models\BusinessContact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Vinkla\Hashids\Facades\Hashids;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Decode directly (auto fail if invalid)
        $userId = decodeIdOrFail($request->user_id, 'user ID');

        $user = User::findOrFail($userId);

        $data = $request->validate([
            'name' => 'required|string',
            //'email' => 'required|email|unique:users,email,' . $user->id,
            //'mobile' => 'required|unique:users,mobile,' . $user->id,
            //'password' => 'nullable|min:6',

            'business_name' => 'required',
            'business_category_id' => 'required',
            'business_sub_category_id' => 'required',

            'contact_person_name' => 'required',
            'contact_number' => 'required',

            'agree_terms' => 'required',
            'confirm_info' => 'required',
        ]);

        DB::beginTransaction();

        try {

            // Update User
            $user->update([
                'name' => $data['name'],
                //'email' => $data['email'],
                //'mobile' => $data['mobile'],
                'dob' => $request->dob,
                'gender' => $request->gender,
                'wallet1' => $request->wallet1,
                'wallet2' => $request->wallet2,
                'wallet3' => $request->wallet3,
            ]);

            // Update Password if exists
            // if (!empty($data['password'])) {
            //     $user->update([
            //         'password' => Hash::make($data['password'])
            //     ]);
            // }

            // Ensure Business exists
            $business = Business::where('user_id', $user->id)->firstOrFail();

            $business->update([
                'sponsor_id' => $request->sponsor_id,
                'business_name' => $request->business_name,
                'business_category_id' => $request->business_category_id,
                'business_sub_category_id' => $request->business_sub_category_id,
                'years_in_business' => $request->years_in_business,
                'gst_number' => $request->gst_number,
                'pan_number' => $request->pan_number,
                //'fssai_license' => $request->fssai_license,
                'registration_number' => $request->registration_number,
            ]);

            // Address
            $address = BusinessAddress::where('business_id', $business->id)->firstOrFail();

            $address->update($request->only([
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'pincode',
                'landmark',
                'google_map_location'
                //'latitude',
                //'longitude'
            ]));

            // Contact
            $contact = BusinessContact::where('business_id', $business->id)->firstOrFail();

            $contact->update($request->only([
                'contact_person_name',
                'contact_number'
            ]));

            // Agreement
            $agreement = BusinessAgreement::where('business_id', $business->id)->firstOrFail();

            $agreement->update($request->only([
                'agree_terms',
                'confirm_info'
            ]));

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

            return response()->json([
                'status' => true,
                'message' => 'Updated successfully',
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function checkGst(Request $request)
    {
        try {

            // Decode user_id FIRST
            $decoded = Hashids::decode($request->user_id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid user ID'
                ], 400);
            }

            // Replace with real ID
            $request->merge([
                'user_id' => $decoded[0]
            ]);

            // Now validate
            $request->validate([
                'user_id' => 'required|integer|exists:users,id'
            ]);

            // Query
            $exists = Business::where('user_id', $request->user_id)
                ->whereNotNull('gst_number')
                ->where('gst_number', '!=', '')
                ->exists();

            return response()->json([
                'status' => true,
                'exists' => $exists,
                'message' => $exists
                    ? 'GST already registered'
                    : 'GST not registered'
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
     * Get Category Dropdown as per Businss Category
     */
    public function CategoryDropdown(Request $request)
    {
        try {
            // Decode ID
            $decoded = Hashids::decode($request->business_category_id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid business category ID'
                ], 400);
            }

            $businessCategoryId = $decoded[0];

            // Validation
            $request->merge(['business_category_id' => $businessCategoryId]);

            $request->validate([
                'business_category_id' => 'required|exists:business_categories,id'
            ]);

            // Fetch categories
            $categories = BusinessCategory::findOrFail($businessCategoryId)
                ->categories()
                ->select('categories.id', 'categories.name')
                ->distinct()
                ->get();

            // RETURN WITH RESOURCE
            return response()->json([
                'status' => true,
                'data' => CategoryResource::collection($categories)
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


}
