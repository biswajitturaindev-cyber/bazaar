<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class BusinessCategoryMappingResource extends JsonResource
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
            'business_category' => [
                'id' => Hashids::encode($this->businessCategory?->id),
                'name' => $this->businessCategory?->name,
            ],
            'business_sub_category' => [
                'id' => Hashids::encode($this->businessSubCategory?->id),
                'name' => $this->businessSubCategory?->name,
            ],
            'product_category' => [
                'id' => Hashids::encode($this->category?->id),
                'name' => $this->category?->name,
            ],
            'status' => $this->status,
            'status_label' => $this->status ? 'Active' : 'Inactive',
            'created_at' => $this->created_at,
        ];
    }
}
