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

            // Product Info
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,

            // Pricing
            'mrp' => $this->mrp,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'discount' => $this->discount,
            'final_price' => $this->final_price,

            // Dates
            'manufacture_date' => $this->manufacture_date,
            'expiry_date' => $this->expiry_date,

            // Image
            'image' => $this->image
                ? asset('storage/' . $this->image)
                : null,

            // Status
            'status' => $this->status,
            'status_label' => $this->statusLabel($this->status),

            'business_category' => $this->businessCategory ? [
                'id' => Hashids::encode($this->businessCategory->id),
                'name' => $this->businessCategory->name,
            ] : null,

            'business_sub_category' => $this->businessSubCategory ? [
                'id' => Hashids::encode($this->businessSubCategory->id),
                'name' => $this->businessSubCategory->name,
            ] : null,

            'product_category' => $this->category ? [
                'id' => Hashids::encode($this->category->id),
                'name' => $this->category->name,
            ] : null,

            'product_sub_category' => $this->subCategory ? [
                'id' => Hashids::encode($this->subCategory->id),
                'name' => $this->subCategory->name,
            ] : null,

            'product_sub_sub_category' => $this->subSubCategory ? [
                'id' => Hashids::encode($this->subSubCategory->id),
                'name' => $this->subSubCategory->name,
            ] : null,

            'hsn' => $this->hsn ? [
                'id' => Hashids::encode($this->hsn->id),
                'code' => $this->hsn->hsn_code,
            ] : null,

            // Attributes
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
            2 => 'Unapproved',
            default => 'Unknown',
        };
    }
}
