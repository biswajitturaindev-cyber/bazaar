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

            'delivery_type' => $this->delivery_type,
            'delivery_radius' => (float) ($this->delivery_radius ?? 0),

            'serviceable_pincode' => $this->serviceable_pincode
                ? explode(',', $this->serviceable_pincode)
                : [],

            'timings' => $this->whenLoaded('timings', function () {
                return $this->timings->map(function ($timing) {
                    return [
                        'id' => $timing->id,
                        'opening_time' => $timing->opening_time,
                        'closing_time' => $timing->closing_time,
                    ];
                });
            }),

            'status' => $this->status,
            'status_label' => $this->status ? 'Active' : 'Inactive',

            'is_open_now' => $this->isOpenNow(),

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
