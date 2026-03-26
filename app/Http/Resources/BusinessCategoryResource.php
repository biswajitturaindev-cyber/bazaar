<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image
                ? asset('storage/business_category/' . $this->image)
                : null,
            'status' => $this->status,
            'status_label' => $this->status == 1 ? 'Active' : 'Inactive',
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
