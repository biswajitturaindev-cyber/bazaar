<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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

            'name' => $this->name,
            'description' => $this->description,

            // Category
            'category' => $this->category?->name,
            'sub_category' => $this->subCategory?->name,
            'sub_sub_category' => $this->subSubCategory?->name,

            // HSN
            'hsn_code' => $this->hsn?->hsn_code,
            'gst_percent' => $this->gst_percent,

            // Pricing
            'mrp' => $this->mrp,
            'selling_price' => $this->selling_price,
            'discount' => $this->discount,
            'final_price' => $this->final_price,

            // Status
            'status' => $this->status,

            // Images
            'images' => $this->images->map(function ($img) {
                return $img->image_path
                    ? asset('storage/' . $img->image_path)
                    : null;
            }),

            // Attributes
            'attributes' => $this->attributeValues->map(function ($attr) {
                return [
                    'attribute' => $attr->attribute->name ?? null,
                    'value' => $attr->value->value ?? null,
                ];
            }),

            'created_at' => $this->created_at,
        ];
    }
}
