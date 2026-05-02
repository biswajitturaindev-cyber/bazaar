<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class MemberProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $variant = $this->primaryVariant;
        $image   = $variant?->images->first();

        return [
            'product_id' => Hashids::encode($this->id),

            'name' => $this->name ?? null,
            'description' => $this->description ?? null,

            // Category IDs
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

            // ✅ ALWAYS RETURN PRIMARY VARIANT
            'primary_variant' => $variant
                ? new VariantResource($variant)
                : null,

            // Pricing
            'mrp' => (float) ($variant?->mrp ?? 0),
            'cost_price' => (float) ($variant?->cost_price ?? 0),
            'selling_price' => (float) ($variant?->selling_price ?? 0),
            'discount' => (float) ($variant?->discount ?? 0),
            'final_price' => (float) ($variant?->final_price ?? 0),

            // Image
            'image' => $image
                ? url('storage/' . $image->image_medium)
                : null,

            // Status
            'status' => $this->status,
            'status_label' => match ($this->status) {
                1 => 'Active',
                2 => 'Unapproved',
                default => 'Inactive',
            },

            'product_type' => $this->product_type ?? class_basename($this->resource),

            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
        ];
    }
}