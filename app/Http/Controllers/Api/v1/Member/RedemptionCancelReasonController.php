<?php

namespace App\Http\Controllers\Api\v1\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\RedemptionCancelReasonResource;
use App\Models\RedemptionCancelReason;
use Illuminate\Http\Request;

class RedemptionCancelReasonController extends Controller
{
    public function index()
    {
        $reasons = RedemptionCancelReason::where(
            'status',
            1
        )->get();

        return response()->json([
            'success' => true,
            'message' => 'Cancel reasons fetched successfully.',
            'data' => RedemptionCancelReasonResource::collection(
                $reasons
            ),
        ]);
    }
}
