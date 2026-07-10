<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class SubCategoryItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'category_id' => Hashids::encode($this->category_id),
            'sub_category_id' => Hashids::encode($this->sub_category_id),
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => $this->status == 1 ? 'Active' : 'Inactive',
            'image' => $this->image
                ? asset('storage/subcategoryitem/' . $this->image)
                : null,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
