<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KycDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,

            'owner_photo' => $this->fileUrl($this->owner_photo),
            'shop_photo' => $this->fileUrl($this->shop_photo),
            'pan_card' => $this->fileUrl($this->pan_card),
            'gst_certificate' => $this->fileUrl($this->gst_certificate),
            'trade_license' => $this->fileUrl($this->trade_license),
            'fssai_license' => $this->fileUrl($this->fssai_license),
            'address_proof' => $this->fileUrl($this->address_proof),

            'created_at' => $this->created_at,
        ];
    }

    private function fileUrl($path)
    {
        return $path ? asset('storage/' . $path) : null;
    }
}
