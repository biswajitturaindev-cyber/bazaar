<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\KycDetail;
use App\Models\User;
use Illuminate\Http\Request;

class KycDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $columns = [
                0 => 'id',
                1 => 'business_id',
            ];

            $totalData = KycDetail::count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')] ?? 'id';
            $dir = $request->input('order.0.dir', 'desc');

            $query = KycDetail::with('business.user');

            // Search
            if ($search = $request->input('search.value')) {

                $query->whereHas('business.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('vendor_id', 'like', "%{$search}%");
                });

            }

            $totalFiltered = $query->count();

            $kycs = $query
                ->orderBy($order, $dir)
                ->offset($start)
                ->limit($limit)
                ->get();

            $data = [];

            foreach ($kycs as $kyc) {

                $statusMap = [
                    1 => ['bg-green-100 text-green-700', 'Approved'],
                    2 => ['bg-red-100 text-red-700', 'Rejected'],
                    0 => ['bg-yellow-100 text-yellow-700', 'Pending'],
                ];

                $makeDocument = function ($image, $status) use ($statusMap) {

                    [$class, $label] = $statusMap[$status] ?? $statusMap[0];

                    $src = $image
                        ? asset('storage/' . $image)
                        : asset('images/no-image.png');

                    return '
                        <div class="text-center">
                            <img src="' . $src . '"
                                class="w-10 h-10 rounded border object-cover mx-auto">

                            <div class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded ' . $class . '">
                                    ' . $label . '
                                </span>
                            </div>
                        </div>';
                };

                $action = '
                    <a href="' . route('kyc-details.edit', $kyc->id) . '"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded">
                        Edit
                    </a>';

                $data[] = [
                    '',
                    ($kyc->business->user->name ?? '-') .
                        ' (' . ($kyc->business->user->vendor_id ?? '-') . ')',

                    $makeDocument($kyc->owner_photo, $kyc->owner_photo_status),
                    $makeDocument($kyc->shop_photo, $kyc->shop_photo_status),
                    $makeDocument($kyc->pan_card, $kyc->pan_card_status),
                    $makeDocument($kyc->gst_certificate, $kyc->gst_certificate_status),
                    $makeDocument($kyc->trade_license, $kyc->trade_license_status),
                    $makeDocument($kyc->fssai_license, $kyc->fssai_license_status),
                    $makeDocument($kyc->address_proof, $kyc->address_proof_status),

                    $action,
                ];
            }

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);
        }

        return view('admin.kyc.index');
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
        $userId = Business::where('id', $kyc->business_id)->value('user_id');

        User::where('id', $userId)
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
