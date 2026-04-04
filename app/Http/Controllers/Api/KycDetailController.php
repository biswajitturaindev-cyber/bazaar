<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KycDetailResource;
use App\Models\KycDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Vinkla\Hashids\Facades\Hashids;

class KycDetailController extends Controller
{
    protected $fileFields = [
        'owner_photo',
        'shop_photo',
        'pan_card',
        'gst_certificate',
        'trade_license',
        'fssai_license',
        'address_proof'
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // try {
        //     $data = KycDetail::latest()->get();
        //     return KycDetailResource::collection($data);

        // } catch (Exception $e) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Failed to fetch KYC details',
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
                'business_id'     => 'required|exists:businesses,id|unique:kyc_details,business_id',
                'owner_photo'     => 'required|file',
                'shop_photo'      => 'required|file',
                'pan_card'        => 'required|file',
                'gst_certificate' => 'nullable|file',
                'trade_license'   => 'nullable|file',
                'fssai_license'   => 'nullable|file',
                'address_proof'   => 'nullable|file',
            ]);

            foreach ($data as $key => $value) {
                if ($request->hasFile($key)) {
                    $data[$key] = $this->uploadFile($request->file($key));
                }
            }

            $kyc = KycDetail::create($data);

            return response()->json([
                'status' => true,
                'message' => 'KYC created',
                'data' => new KycDetailResource($kyc)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create KYC',
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

            $kyc = KycDetail::findOrFail($id);
            return new KycDetailResource($kyc);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'KYC not found',
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

            $kyc = KycDetail::findOrFail($id);

            $data = $request->validate([
                'owner_photo' => 'nullable|file',
                'shop_photo' => 'nullable|file',
                'pan_card' => 'nullable|file',
                'gst_certificate' => 'nullable|file',
                'trade_license' => 'nullable|file',
                'fssai_license' => 'nullable|file',
                'address_proof' => 'nullable|file',
            ]);


            foreach ($this->fileFields as $field) {

                if ($request->hasFile($field)) {

                    // delete old file safely
                    $this->deleteOldFile($kyc->{$field});

                    // upload new file
                    $data[$field] = $this->uploadFile($request->file($field));
                }
            }

            $kyc->update($data);

            return response()->json([
                'status' => true,
                'message' => 'KYC updated',
                'data' => new KycDetailResource($kyc)
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update KYC',
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
            $kyc = KycDetail::findOrFail($id);

            // delete all files using array
            foreach ($this->fileFields as $field) {
                $this->deleteOldFile($kyc->{$field});
            }

            $kyc->delete();

            return response()->json([
                'status' => true,
                'message' => 'KYC deleted'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete KYC',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload helper
     */
    private function uploadFile($file, $folder = 'kyc')
    {
        $manager = new ImageManager(new Driver());
        $filename = Str::uuid();

        // Image handling (jpg, png, jpeg)
        if (in_array($file->extension(), ['jpg', 'jpeg', 'png', 'webp'])) {

            // LARGE (300x300)
            $large = $manager->read($file)->cover(300, 300);
            $largeWebp = compressToTargetSize($large, 30);

            Storage::disk('public')->put(
                "{$folder}/{$filename}.webp",
                $largeWebp
            );

            return "{$folder}/{$filename}.webp";
        }

        // fallback
        return $file->store($folder, 'public');
    }

    /**
     * Delete old file
     */
    private function deleteOldFile($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
