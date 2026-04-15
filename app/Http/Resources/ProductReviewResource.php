<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Resources\ProductReviewAttributeResource;

class ProductReviewResource extends JsonResource
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

            // FK
            'business_id' => $this->business_id ? Hashids::encode($this->business_id) : null,
            'business_sub_category_id' => $this->business_sub_category_id ? Hashids::encode($this->business_sub_category_id) : null,
            'category_id' => $this->category_id ? Hashids::encode($this->category_id) : null,
            'sub_category_id' => $this->sub_category_id ? Hashids::encode($this->sub_category_id) : null,
            'sub_sub_category_id' => $this->sub_sub_category_id ? Hashids::encode($this->sub_sub_category_id) : null,

            // Product Info
            'name' => $this->name,
            'sku' => $this->sku,
            'hsn_id' => $this->hsn_id ? Hashids::encode($this->hsn_id) : null,
            'description' => $this->description,

            // Pricing
            'mrp' => $this->mrp,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'discount' => $this->discount,

            // Dates
            'manufacture_date' => $this->manufacture_date,
            'expiry_date' => $this->expiry_date,

            // Image
            'image' => $this->image
                ? asset('storage/' . $this->image)
                : null,

            // Status with label
            'status' => $this->status,
            'status_label' => $this->statusLabel($this->status),

            // Relation (like your example)
            'product_attributes' => ProductReviewAttributeResource::collection(
                $this->whenLoaded('productAttributes')
            ),

            // Timestamp
            'created_at' => $this->created_at,
        ];
    }

    /**
     * Status Label Helper
     */
    private function statusLabel($status): string
    {
        return match ($status) {
            1 => 'Active',
            2 => 'Inactive',
            default => 'Unknown',
        };
    }
}
