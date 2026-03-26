<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => $this->status == 1 ? 'Active' : 'Inactive',
            'commission' => $this->commission,
            'image' => $this->image
                ? asset('storage/category/' . $this->image)
                : null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
