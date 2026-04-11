<?php

namespace App\Http\Resources;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterProductResource extends JsonResource
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
            'name' => $this->name,

            'category' => $this->category?->name,
            'sub_category' => $this->subCategory?->name,
            'sub_sub_category' => $this->subSubCategory?->name,

            'hsn' => $this->hsn?->hsn_code,

            'product_price' => $this->product_price,
            'selling_price' => $this->selling_price,

            'description' => $this->description,

            // Full image URL
            'image' => $this->image
                ? asset('storage/' . $this->image)
                : null,

            'status' => $this->status,

            'created_at' => $this->created_at?->format('d-m-Y'),
        ];
    }
}
