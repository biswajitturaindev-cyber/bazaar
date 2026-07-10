<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepositResource;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
                    'business_id'            => 'required',
                    'company_account_id'     => 'required|integer',
                    'amount'                 => 'required|numeric|min:1',
                    'payment_method'         => 'required|in:1,2,3',
                    'transaction_id'         => 'nullable|string|max:100',
                    'ref_id'                 => 'required|string|max:100',
                    'payment_proof'          => 'required|image|mimes:jpg,jpeg,png,pdf|max:5120',
                    'user_note'              => 'nullable|string|max:500',
                ];

        try {
             $data = $request->validate($rules);
             $data['business_id'] = decodeIdOrFail($data['business_id'], 'Invalid Business ID');
            if ($request->hasFile('payment_proof')) {

                $manager = new ImageManager(new Driver());

                $filename = time() . '_' . uniqid();

                $image = $manager->read($request->file('payment_proof'))
                    ->scaleDown(width: 1200);

                Storage::disk('public')->put(
                    "deposits/{$filename}.webp",
                    compressToTargetSize($image, 50)
                );

                $data['payment_proof'] = "deposits/{$filename}.webp";
            }

            $deposit = Deposit::create([
                'business_id'            => $data['business_id'],
                'company_account_id'     => $data['company_account_id'],
                'amount'                 => $data['amount'],
                'payment_method'         => $data['payment_method'],
                'transaction_id'         => $data['transaction_id'] ?? null,
                'ref_id'                 => $data['ref_id'] ?? null,
                'payment_proof'          => $data['payment_proof'],
                'user_note'              => $data['user_note'] ?? null,
                'status'                 => 0,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Deposit request submitted successfully.',
                'data'    => new DepositResource($deposit),
            ], 201);

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error('Deposit Store Error: '.$e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
