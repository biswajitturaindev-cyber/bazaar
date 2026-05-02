<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class VariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'variant_id' => Hashids::encode($this->id),

            'sku' => $this->sku,
            'barcode' => $this->barcode,

            'mrp' => $this->mrp,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'discount' => $this->discount,
            'final_price' => $this->final_price,

            'is_primary' => $this->is_primary,

            // ALL STOCKS
            'stocks' => ProductVendorStockResource::collection(
                $this->whenLoaded('stocks')
            ),

            // TOTAL STOCK
            'total_stock' => $this->stocks->sum('stock'),

            'attributes' => AttributeRelationResource::collection(
                $this->whenLoaded('attributes')
            ),

            'images' => ProductImageResource::collection(
                $this->whenLoaded('images')
            ),

            'meta' => new ProductVariantMetaResource(
                $this->whenLoaded('meta')
            ),
        ];
    }
}
