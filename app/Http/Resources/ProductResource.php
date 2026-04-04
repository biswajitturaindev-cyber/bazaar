<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,
            'description' => $this->description,

            // User
            'user_id' => $this->user_id,

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

            // IMAGES
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'large' => $img->image_large
                            ? asset('storage/' . $img->image_large)
                            : null,

                        'medium' => $img->image_medium
                            ? asset('storage/' . $img->image_medium)
                            : null,

                        'small' => $img->image_small
                            ? asset('storage/' . $img->image_small)
                            : null,
                    ];
                });
            }, []),

            // ATTRIBUTES
            'attributes' => $this->whenLoaded('attributes', function () {
                return $this->attributes->map(function ($attr) {
                    return [
                        'attribute' => $attr->attribute->name ?? null,
                        'value' => $attr->value->value ?? null,
                    ];
                });
            }, []),

            'created_at' => $this->created_at,
        ];
    }
}
