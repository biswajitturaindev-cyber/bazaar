<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $variant = $this->primaryVariant;
        $image   = $variant?->images->first();

        return [
            'product_id' => isset($this->id)
                ? Hashids::encode($this->id)
                : ($this->product_id ?? null),

            'name' => $this->name ?? null,
            'description' => $this->description ?? null,
            'status' => $this->status ?? null,
            'status_label' => config('product.status')[$this->status] ?? 'Unknown',

            'category' => [
                'id' => Hashids::encode($this->category?->id),
                'label' => $this->category?->name,
            ],

            // Sub Category
            'sub_category' => [
                'id' => $this->subCategory?->id ? Hashids::encode($this->subCategory->id) : null,
                'label' => $this->subCategory?->name,
            ],

            // Sub Sub Category (optional)
            'sub_sub_category' => [
                'id' => $this->subSubCategory?->id ? Hashids::encode($this->subSubCategory->id) : null,
                'label' => $this->subSubCategory?->name,
            ],

            // HSN
            'hsn' => [
                'id' => $this->hsn?->id ? Hashids::encode($this->hsn->id) : null,
                'label' => $this->hsn?->hsn_code,
            ],

            // primary (highlight)
            // 'primary_variant' => $this->whenLoaded('primaryVariant', function () {
            //     return new VariantResource($this->primaryVariant);
            // }),

            // all variants (full data)
            'variants' => $this->whenLoaded('variants', function () {
                return VariantResource::collection($this->variants);
            }),

            // optional: quick access fields (VERY useful)
            'price' => optional($this->primaryVariant)->selling_price,
            'mrp' => optional($this->primaryVariant)->mrp,
            'image' => $image
            ? url('storage/' . $image->image_medium)
            : null,
            'table' => $this->getTable(),
        ];
    }
}
