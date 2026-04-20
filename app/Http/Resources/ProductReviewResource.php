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
            // ALL IDS
            'id' => $this->id ? Hashids::encode($this->id) : null,
            'business_id' => $this->business_id ? Hashids::encode($this->business_id) : null,
            'business_category_id' => $this->business_category_id ? Hashids::encode($this->business_category_id) : null,
            'business_sub_category_id' => $this->business_sub_category_id ? Hashids::encode($this->business_sub_category_id) : null,
            'category_id' => $this->category_id ? Hashids::encode($this->category_id) : null,
            'sub_category_id' => $this->sub_category_id ? Hashids::encode($this->sub_category_id) : null,
            'sub_sub_category_id' => $this->sub_sub_category_id ? Hashids::encode($this->sub_sub_category_id) : null,
            'hsn_id' => $this->hsn_id ? Hashids::encode($this->hsn_id) : null,

            // BASIC FIELDS (ALL FROM TABLE)
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,

            // PRICE FIELDS
            'mrp' => $this->mrp,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'discount' => $this->discount,
            'final_price' => $this->final_price,

            // DATES
            'manufacture_date' => $this->manufacture_date,
            'expiry_date' => $this->expiry_date,

            // IMAGE
            'image' => $this->image ? asset('storage/' . $this->image) : null,

            // STATUS
            'status' => $this->status,
            'status_label' => $this->statusLabel($this->status),

            // TYPE
            'type' => $this->type,

            // RELATIONS (ID + LABEL)
            'business_category' => $this->businessCategory ? [
                'id' => Hashids::encode($this->businessCategory->id),
                'label' => $this->businessCategory->name,
            ] : null,

            'business_sub_category' => $this->businessSubCategory ? [
                'id' => Hashids::encode($this->businessSubCategory->id),
                'label' => $this->businessSubCategory->name,
            ] : null,

            'product_category' => $this->category ? [
                'id' => Hashids::encode($this->category->id),
                'label' => $this->category->name,
            ] : null,

            'product_sub_category' => $this->subCategory ? [
                'id' => Hashids::encode($this->subCategory->id),
                'label' => $this->subCategory->name,
            ] : null,

            'product_sub_sub_category' => $this->subSubCategory ? [
                'id' => Hashids::encode($this->subSubCategory->id),
                'label' => $this->subSubCategory->name,
            ] : null,

            'hsn' => $this->hsn ? [
                'id' => Hashids::encode($this->hsn->id),
                'label' => $this->hsn->hsn_code,
            ] : null,

            // REVIEW ATTRIBUTES
            'product_attributes' => $this->type === 'unapprove'
                ? collect($this->productAttributes ?? [])->map(function ($attr) {
                    return [
                        'id' => Hashids::encode($attr->id),

                        'attribute' => [
                            'id' => Hashids::encode($attr->attribute_id),
                            'label' => optional($attr->attribute)->name,
                        ],

                        'attribute_value' => [
                            'id' => Hashids::encode($attr->attribute_value_id),
                            'label' => optional($attr->attributeValue)->value,
                        ],

                        'stock' => $attr->stock,
                        'price' => $attr->price,
                    ];
                })
                : [],

            // PRODUCT ATTRIBUTES
            'attributes' => $this->type === 'approve'
                ? collect($this->attributes ?? [])->map(function ($attr) {
                    return [
                        'id' => Hashids::encode($attr->id),

                        'attribute' => [
                            'id' => Hashids::encode($attr->attribute_id),
                            'label' => optional($attr->attribute)->name,
                        ],

                        'attribute_value' => [
                            'id' => Hashids::encode($attr->attribute_value_id),
                            'label' => optional($attr->attributeValue)->value,
                        ],

                        'stock' => $attr->stock,
                        'price' => $attr->price,
                    ];
                })
                : [],
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
