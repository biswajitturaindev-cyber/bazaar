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
            'product_id' => isset($this->id)
                ? Hashids::encode($this->id)
                : ($this->product_id ?? null),

            'name' => $this->name ?? null,
            'description' => $this->description ?? null,
            'status' => $this->status ?? null,
            'status_label' => config('product.status')[$this->status] ?? 'Unknown',

            'category_id' => $this->category_id ?? null,
            'sub_category_id' => $this->sub_category_id ?? null,

            // ✅ primary variant (single)
            'variant' => $this->whenLoaded('primaryVariant', function () {
                return new VariantResource($this->primaryVariant);
            }),

            // optional: quick access fields (VERY useful)
            'price' => optional($this->primaryVariant)->selling_price,
            'mrp' => optional($this->primaryVariant)->mrp,
            'image' => optional($this->primaryVariant?->images->first())->image_path ?? null,

            'table' => $this->getTable(),
        ];
    }
}
