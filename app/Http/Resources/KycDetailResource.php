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

            'id' => Hashids::encode($this->id),
            'business_id' => Hashids::encode($this->business_id),
            'owner_photo' => $this->document(
                $this->owner_photo,
                null,
                $this->owner_photo_status
            ),
            'shop_photo' => $this->document(
                $this->shop_photo,
                null,
                $this->shop_photo_status
            ),
            'pan_card' => $this->document(
                $this->pan_card,
                $this->pan_no,
                $this->pan_card_status
            ),
            'gst_certificate' => [
                ...$this->document(
                    $this->gst_certificate,
                    $this->gst_no,
                    $this->gst_certificate_status
                ),
                'state_code' => $this->gst_state_code,
                'gst_address' => $this->gst_address,
            ],
            'trade_license' => $this->document(
                $this->trade_license,
                $this->trade_license_no,
                $this->trade_license_status
            ),
            'fssai_license' => $this->document(
                $this->fssai_license,
                $this->fssai_license_no,
                $this->fssai_license_status
            ),
            'address_proof' => $this->document(
                $this->address_proof,
                null,
                $this->address_proof_status
            ),

            'commission_distribution' => (bool) $this->commission_distribution,
            'commission_distribution_label' => $this->commission_distribution
                ? 'Accepted'
                : 'Not Accepted',
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Format document response
     */
    private function document($path, $number, $status)
    {
        $status = (int) $status;

        return [
            'url' => $path
                ? asset('storage/' . $path)
                : null,
            'number' => $number,
            'status' => $status,
            'status_label' => $this->statusLabel($status),
        ];
    }

    /**
     * Status Label
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
