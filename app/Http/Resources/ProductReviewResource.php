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
        $data = [
            'id' => Hashids::encode($this->id),
            'business_id' => Hashids::encode($this->business_id),
            'business_category_id' => Hashids::encode($this->business_category_id),
            'category_id' => Hashids::encode($this->category_id),
            'sub_category_id' => Hashids::encode($this->sub_category_id),
            'sub_sub_category_id' => Hashids::encode($this->sub_sub_category_id),

            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,

            'mrp' => $this->mrp,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'discount' => $this->discount,
            'final_price' => $this->final_price,

            'manufacture_date' => $this->manufacture_date,
            'expiry_date' => $this->expiry_date,

            'image' => $this->image
                ? asset('storage/' . $this->image)
                : null,

            'status' => $this->status,
            'type' => $this->type,
        ];

        // REVIEW
        if ($this->type === 'unapprove') {

            $data['product_attributes'] = $this->productAttributes
                ? $this->productAttributes->map(function ($attr) {
                    return [
                        'id' => Hashids::encode($attr->id),
                        'product_review_id' => Hashids::encode($attr->product_review_id),
                        'attribute_id' => Hashids::encode($attr->attribute_id),
                        'attribute_value_id' => Hashids::encode($attr->attribute_value_id),
                        'stock' => $attr->stock,
                        'price' => $attr->price,
                    ];
                })
                : [];

            $data['attributes'] = [];
        }

        // PRODUCT
        else {

            $data['attributes'] = collect($this->attributes ?? [])
                ->map(function ($attr) {
                    return [
                        'id' => Hashids::encode($attr->id),
                        'attribute_id' => Hashids::encode($attr->attribute_id),
                        'attribute_value_id' => Hashids::encode($attr->attribute_value_id),
                        'stock' => $attr->stock,
                        'price' => $attr->price,
                    ];
                });

            $data['product_attributes'] = [];
        }

        return $data;
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
