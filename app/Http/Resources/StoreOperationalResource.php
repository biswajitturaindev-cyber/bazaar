<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;
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
            'id' => Hashids::encode($this->id),

            'delivery_type' => $this->delivery_type,
            'delivery_radius' => (float) ($this->delivery_radius ?? 0),

            'serviceable_pincode' => $this->serviceable_pincode,

            'timings' => $this->timings->map(function ($timing) {
                return [
                    'id' => Hashids::encode($timing->id),
                    'opening_time' => $timing->opening_time,
                    'closing_time' => $timing->closing_time,
                ];
            }),

            'status' => $this->status,
            'status_label' => $this->status ? 'Active' : 'Inactive',

            'is_open_now' => $this->isOpenNow(),

            'shop_status' => $this->business?->shop_status,
            'working_days' => $this->business?->working_days,

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
