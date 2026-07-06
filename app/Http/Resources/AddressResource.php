<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class AddressResource extends JsonResource
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
            'business_id' => Hashids::encode($this->business_id),

            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => Hashids::encode($this->city),
            'state' => Hashids::encode($this->state),
            'city_name' => optional($this->cityDetail)->name,
            'state_name' => optional($this->stateDetail)->name,
            'pincode' => $this->pincode,
            'landmark' => $this->landmark,

            'google_map_location' => $this->google_map_location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
