<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class KycDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => Hashids::encode($this->business_id),

            // Documents with status
            'owner_photo' => $this->document($this->owner_photo, $this->owner_photo_status),
            'shop_photo' => $this->document($this->shop_photo, $this->shop_photo_status),
            'pan_card' => $this->document($this->pan_card, $this->pan_card_status),
            'gst_certificate' => $this->document($this->gst_certificate, $this->gst_certificate_status),
            'trade_license' => $this->document($this->trade_license, $this->trade_license_status),
            'fssai_license' => $this->document($this->fssai_license, $this->fssai_license_status),
            'address_proof' => $this->document($this->address_proof, $this->address_proof_status),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     *  Format document with status
     */
    private function document($path, $status)
    {
        $status = (int) $status;

        return [
            'url' => $path ? asset('storage/' . $path) : null,
            'status' => $status,
            'status_label' => $this->statusLabel($status),
        ];
    }

    /**
     * Status Label Helper
     */
    private function statusLabel($status)
    {
        return match ((int) $status) {
            1 => 'Approved',
            2 => 'Rejected',
            default => 'Pending',
        };
    }
}
