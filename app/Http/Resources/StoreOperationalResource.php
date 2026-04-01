<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreOperationalResource extends JsonResource
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

            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,

            'delivery_type' => $this->delivery_type,
            'delivery_radius' => (float) $this->delivery_radius,

            'serviceable_pincode' => $this->serviceable_pincode,

            'status' => $this->status,
            'is_open_now' => $this->isOpenNow(),

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
