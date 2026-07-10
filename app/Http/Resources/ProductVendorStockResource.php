<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class ProductVendorStockResource extends JsonResource
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

            'business_id' => $this->business_id
                ? Hashids::encode($this->business_id)
                : null,

            // optional (if relation exists)
            'business_name' => $this->whenLoaded('business', fn () => $this->business->business_name),

            'stock' => (int) $this->stock,

            // optional but useful
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
