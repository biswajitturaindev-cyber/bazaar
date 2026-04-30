<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => Hashids::encode($this->product_id),

            'variants' => VariantResource::collection($this->whenLoaded('variants')),

            'table' => $this->table ?? null,
        ];
    }
}
