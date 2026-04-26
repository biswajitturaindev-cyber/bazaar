<?php

namespace App\Http\Resources;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberProductResource extends JsonResource
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
            'sku' => $this->sku,

            'image' => $this->image
                ? asset('storage/products/' . $this->image)
                : null,

            'description' => $this->description,

            // Category Structure
            'business_category_id' => $this->business_category_id
                ? Hashids::encode($this->business_category_id)
                : null,

            'business_sub_category_id' => $this->business_sub_category_id
                ? Hashids::encode($this->business_sub_category_id)
                : null,

            'category_id' => $this->category_id
                ? Hashids::encode($this->category_id)
                : null,

            'sub_category_id' => $this->sub_category_id
                ? Hashids::encode($this->sub_category_id)
                : null,

            'sub_sub_category_id' => $this->sub_sub_category_id
                ? Hashids::encode($this->sub_sub_category_id)
                : null,

            // Pricing
            'mrp' => (float) $this->mrp,
            'cost_price' => (float) $this->cost_price,
            'selling_price' => (float) $this->selling_price,
            'discount' => (float) $this->discount,
            'final_price' => (float) $this->final_price,

            // Dates
            'manufacture_date' => $this->manufacture_date,
            'expiry_date' => $this->expiry_date,

            // Status
            'status' => $this->status,
            'status_label' => match ($this->status) {
                1 => 'Active',
                2 => 'Unapproved',
                default => 'Inactive',
            },

            // Product Type (VERY IMPORTANT for multi-table)
            'product_type' => class_basename($this->resource),

            // Attributes (if loaded)
            'attributes' => $this->whenLoaded('attributes', function () {
                return $this->attributes->map(function ($attr) {
                    return [
                        'attribute_id' => Hashids::encode($attr->attribute_id),
                        'attribute_value_id' => Hashids::encode($attr->attribute_value_id),
                        'price' => (float) $attr->price,
                        'stock' => (int) $attr->stock,
                    ];
                });
            }),

            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}
