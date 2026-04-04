<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class BusinessResource extends JsonResource
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
            'business_name' => $this->business_name,
            'category' => [
                'id' => Hashids::encode($this->business_category_id),
                'name' => $this->category?->name,
            ],

            'sub_category' => [
                'id' => Hashids::encode($this->business_sub_category_id),
                'name' => $this->subCategory?->name,
            ],
            'years_in_business' => $this->years_in_business,

            // Nested relations
            'address' => $this->whenLoaded('address'),
            'contact' => $this->whenLoaded('contact'),
            'agreement' => $this->whenLoaded('agreement'),
            'bankdetail' => $this->whenLoaded('bankDetail'),
            'kycdetail' => $this->whenLoaded('kycDetail'),
        ];
    }
}
