<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycDetail;
use Illuminate\Http\Request;

class KycDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $kycs = KycDetail::with('business')->latest()->paginate(10);
    return view('admin.kyc.index', compact('kycs'));
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
    $kyc = KycDetail::findOrFail($id);

    return view('admin.kyc.edit', compact('kyc'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kyc = KycDetail::findOrFail($id);

        $data = $request->validate([
            'owner_photo_status' => 'required|in:0,1,2',
            'shop_photo_status' => 'required|in:0,1,2',
            'pan_card_status' => 'required|in:0,1,2',
            'gst_certificate_status' => 'nullable|in:0,1,2',
            'trade_license_status' => 'nullable|in:0,1,2',
            'fssai_license_status' => 'nullable|in:0,1,2',
            'address_proof_status' => 'nullable|in:0,1,2',
        ]);

        $kyc->update($data);

        // Auto update main KYC status
        $statuses = collect($data);

        if ($statuses->contains(2)) {
            $kycStatus = 3; // Rejected
        } elseif ($statuses->every(fn($s) => $s == 1)) {
            $kycStatus = 1; // Approved
        } else {
            $kycStatus = 2; // Pending
        }

        // Update user KYC
        $userId = \App\Models\Business::where('id', $kyc->business_id)->value('user_id');

        \App\Models\User::where('id', $userId)
            ->update(['kyc_status' => $kycStatus]);

        return redirect()->route('kyc-details.index')
            ->with('success', 'KYC updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
