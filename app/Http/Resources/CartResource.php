<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class CartResource extends JsonResource
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
            'product_id' => Hashids::encode($this->product_id),
            'product_type' => $this->product_type,
            'product_name' => $this->product_name,
            'image' => $this->image,
            'price' => (float) $this->price,
            'quantity' => (int) $this->quantity,
            'total' => (float) $this->total,

            'attributes' => $this->attributes->map(function ($attr) {
                return [
                    'attribute_id' => Hashids::encode($attr->attribute_id),
                    'attribute_value_id' => Hashids::encode($attr->attribute_value_id),
                    'attribute_name' => $attr->attribute_name,
                    'attribute_value' => $attr->attribute_value,
                    'price' => (float) $attr->price,
                ];
            })
        ];
    }
}
