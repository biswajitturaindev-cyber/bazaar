<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class ProductReviewAttributeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => Hashids::encode($this->id),

            // FK
            'product_review_id' => $this->product_review_id
                ? Hashids::encode($this->product_review_id)
                : null,

            'attribute_id' => $this->attribute_id
                ? Hashids::encode($this->attribute_id)
                : null,

            'attribute_value_id' => $this->attribute_value_id
                ? Hashids::encode($this->attribute_value_id)
                : null,

            // Extra Fields
            'stock' => $this->stock,
            'price' => $this->price,

            'product_review' => new ProductReviewResource(
                $this->whenLoaded('review')
            ),

            // Timestamp
            'created_at' => $this->created_at,
        ];
    }
}
