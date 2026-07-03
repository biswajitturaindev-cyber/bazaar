<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class VendorLoyaltyWalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'business_id' => Hashids::encode($this->business_id),
            'order_id' => $this->order_id,

            'order_type' => $this->order_type,

            'transaction_no' => $this->transaction_no,
            'transaction_type' => $this->transaction_type,
            'source' => $this->source,

            'points' => (float) $this->points,
            'opening_points' => (float) $this->opening_points,
            'closing_points' => (float) $this->closing_points,

            'remarks' => $this->remarks,
            'status' => $this->status,

            'created_by' => $this->created_byl,
            'created_at' => optional($this->created_at)->format('d M Y h:i A'),
        ];
    }
}
