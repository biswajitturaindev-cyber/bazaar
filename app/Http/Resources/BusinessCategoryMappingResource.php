<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' => $this->id,
            'business_category' => [
                'id' => $this->businessCategory?->id,
                'name' => $this->businessCategory?->name,
            ],
            'business_sub_category' => [
                'id' => $this->businessSubCategory?->id,
                'name' => $this->businessSubCategory?->name,
            ],
            'product_category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],
            'status' => $this->status,
            'status_label' => $this->status ? 'Active' : 'Inactive',
            'created_at' => $this->created_at,
        ];
    }
}
