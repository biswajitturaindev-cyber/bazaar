<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorLoyaltyWalletResource;
use App\Models\VendorLoyaltyWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorLoyaltyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $businessId = decodeIdOrFail(
                $request->business_id,
                'Invalid business ID'
            );

            $wallets = DB::table('vendor_loyalty_wallets as vlw')
                ->leftJoin('orders as o', function ($join) {
                    $join->on('vlw.order_id', '=', 'o.id')
                        ->where('vlw.order_type', '=', 'member_order');
                })
                ->where('vlw.business_id', $businessId)
                ->orderByDesc('vlw.id')
                ->select(
                    'vlw.*',
                    'o.order_no',
                    'o.invoice_no'
                )
                ->get();

            $walletBalance = VendorLoyaltyWallet::where('business_id', $businessId)
                ->latest('id')
                ->value('closing_points');

            return response()->json([
                'status' => true,
                'message' => 'Vendor loyalty wallet fetched successfully.',
                'wallet_balance' => (float) ($walletBalance ?? 0),
                'data' => VendorLoyaltyWalletResource::collection($wallets),
            ], 200);

        } catch (\Exception $e) {

            \Log::error('Vendor Loyalty Wallet Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
