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

            'mapped_products' => collect($this->mapped_products ?? [])->map(function ($product) {

                return [
                    'id' => Hashids::encode($product->id),
                    'business_id' => Hashids::encode($product->business_id),
                    'business_category_id' => Hashids::encode($product->business_category_id),

                    'name' => $product->name,
                    'sku' => $product->sku,
                    'description' => $product->description,

                    'mrp' => $product->mrp,
                    'cost_price' => $product->cost_price,
                    'selling_price' => $product->selling_price,
                    'discount' => $product->discount,
                    'final_price' => $product->final_price,

                    'manufacture_date' => $product->manufacture_date,
                    'expiry_date' => $product->expiry_date,

                    'image' => $product->image
                        ? asset('storage/' . $product->image)
                        : null,

                    'status' => $product->status,

                    // FIXED: correct status label
                    'status_label' => match ($product->status) {
                        1 => 'Approved',
                        2 => 'Unapproved',
                        default => 'Unknown',
                    },

                    // OPTIONAL: only if relations exist on product model
                    'business_category' => isset($product->businessCategory) ? [
                        'id' => Hashids::encode($product->businessCategory->id),
                        'name' => $product->businessCategory->name,
                    ] : null,

                    'business_sub_category' => isset($product->businessSubCategory) ? [
                        'id' => Hashids::encode($product->businessSubCategory->id),
                        'name' => $product->businessSubCategory->name,
                    ] : null,

                    'product_category' => isset($product->category) ? [
                        'id' => Hashids::encode($product->category->id),
                        'name' => $product->category->name,
                    ] : null,

                    'product_sub_category' => isset($product->subCategory) ? [
                        'id' => Hashids::encode($product->subCategory->id),
                        'name' => $product->subCategory->name,
                    ] : null,

                    'product_sub_sub_category' => isset($product->subSubCategory) ? [
                        'id' => Hashids::encode($product->subSubCategory->id),
                        'name' => $product->subSubCategory->name,
                    ] : null,

                    'hsn' => isset($product->hsn) ? [
                        'id' => Hashids::encode($product->hsn->id),
                        'code' => $product->hsn->hsn_code,
                    ] : null,

                    // Attributes encoding
                    'attributes' => collect($product->attributes ?? [])->map(function ($attr) {
                        return [
                            'id' => Hashids::encode($attr->id),
                            'attribute_id' => Hashids::encode($attr->attribute_id),
                            'attribute_value_id' => Hashids::encode($attr->attribute_value_id),

                            'stock' => $attr->stock,
                            'price' => $attr->price,
                        ];
                    }),
                ];
            }),

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
