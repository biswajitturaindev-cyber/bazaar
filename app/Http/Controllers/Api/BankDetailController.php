<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankDetailResource;
use App\Models\BankDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Vinkla\Hashids\Facades\Hashids;

class BankDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // try {
        //     $data = BankDetail::latest()->get();
        //     return BankDetailResource::collection($data);

        // } catch (Exception $e) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Failed to fetch bank details',
        //         'error' => $e->getMessage()
        //     ], 500);
        // }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Decode business_id FIRST
            $decoded = Hashids::decode($request->business_id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid business ID'
                ], 400);
            }

            $request->merge([
                'business_id' => $decoded[0] // replace with real ID
            ]);


            $data = $request->validate([
                'business_id' => 'required|exists:businesses,id|unique:bank_details,business_id',
                'account_holder_name' => 'required',
                'bank_name' => 'required',
                'account_number' => 'required',
                'ifsc_code' => 'required',
                'upi_id' => 'nullable',
                'cancelled_cheque' => 'required|file',
            ]);

            // create manager (GD)
            $manager = new ImageManager(new Driver());

            if ($request->hasFile('cancelled_cheque')) {

                $file = $request->file('cancelled_cheque');

                // unique filename
                $filename = Str::uuid();

                // read image
                $image = $manager->read($file);

                // resize
                $resized = $image->cover(150, 150);

                // compress to webp
                $webp = compressToTargetSize($resized, 60);

                // save file
                Storage::disk('public')->put(
                    "bank/{$filename}.webp",
                    $webp
                );

                // save path in DB
                $data['cancelled_cheque'] = "bank/{$filename}.webp";
            }

            $bank = BankDetail::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Bank details created',
                'data' => new BankDetailResource($bank)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create bank details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Decode and replace $id itself
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid ID'
                ], 400);
            }

            $id = $decoded[0]; // overwrite $id

            $bank = BankDetail::findOrFail($id);
            return new BankDetailResource($bank);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Bank detail not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Decode and overwrite $id
            $decoded = Hashids::decode($id);

            if (empty($decoded)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid ID'
                ], 400);
            }

            $id = $decoded[0]; // important

            $bank = BankDetail::findOrFail($id);

            $data = $request->validate([
                'account_holder_name' => 'required',
                'bank_name' => 'required',
                'account_number' => 'required',
                'ifsc_code' => 'required',
                'upi_id' => 'nullable',
                'cancelled_cheque' => 'nullable|file',
            ]);

            // create manager (GD or Imagick)
            $manager = new ImageManager(new Driver());

            if ($request->hasFile('cancelled_cheque')) {

                $file = $request->file('cancelled_cheque');

                // unique filename
                $filename = Str::uuid();

                // read image
                $image = $manager->read($file);

                // resize
                $resized = $image->cover(150, 150);

                // compress to webp
                $webp = compressToTargetSize($resized, 60);

                // save file
                Storage::disk('public')->put(
                    "bank/{$filename}.webp",
                    $webp
                );

                // save path in DB
                $data['cancelled_cheque'] = "bank/{$filename}.webp";
            }

            $bank->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Bank details updated',
                'data' => new BankDetailResource($bank)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update bank details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $bank = BankDetail::findOrFail($id);
            $bank->delete();

            return response()->json([
                'status' => true,
                'message' => 'Bank details deleted'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete bank details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
