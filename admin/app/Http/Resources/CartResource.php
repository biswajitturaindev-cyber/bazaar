<?php

namespace App\Http\Resources;

use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $product = $this->product();
        $variant = $this->productVariant;

        $imageUrl = null;

        // First try eager loaded variant images
        if (
            $variant &&
            $variant->relationLoaded('images') &&
            $variant->images->isNotEmpty()
        ) {

            $image = $variant->images->first();

            if (!empty($image->image_medium)) {
                $imageUrl = asset('storage/' . $image->image_medium);
            }
        }

        // Fallback if image not found
        if (!$imageUrl) {

            $productImage = ProductImage::query()
                ->where('business_category_id', $this->business_category_id)
                ->where('product_id', $this->product_id)
                ->where(function ($query) {
                    $query->where('product_variant_id', $this->product_variant_id)
                        ->orWhereNull('product_variant_id');
                })
                ->latest('id')
                ->first();

            if (
                $productImage &&
                !empty($productImage->image_medium)
            ) {
                $imageUrl = asset(
                    'storage/' . $productImage->image_medium
                );
            }
        }

        $finalPrice = round(
            (float) ($variant->final_price ?? 0),
            2
        );

        return [
            'id' => Hashids::encode($this->id),

            'user' => $this->user_id,

            'business' => [
                'id' => $this->business
                    ? Hashids::encode($this->business->id)
                    : null,

                'name' => $this->business->business_name ?? null,

                'gst_no' => $this->kycDetail?->gst_no,
                'gst_state_code' => $this->kycDetail?->gst_state_code,
                'gst_address' => $this->kycDetail?->gst_address,
            ],

            'business_category_id' => Hashids::encode(
                $this->business_category_id
            ),

            'product_id' => Hashids::encode(
                $this->product_id
            ),

            'product_variant_id' => !empty(
                $this->product_variant_id
            )
                ? Hashids::encode(
                    $this->product_variant_id
                )
                : null,

            'product_name' => $this->product_name,

            'quantity' => (int) $this->quantity,

            'image' => $imageUrl,

            'product' => [
                'name' => $product->name ?? null,
                'final_price' => $finalPrice,
                'image' => $imageUrl,
            ],

            'attributes' => $this->whenLoaded(
                'cartAttributes',
                function () {

                    return $this->cartAttributes->map(
                        function ($attr) {

                            return [
                                'attribute_master_id' => Hashids::encode(
                                    $attr->attribute_master_id
                                ),

                                'attribute_value_id' => Hashids::encode(
                                    $attr->attribute_value_id
                                ),

                                'attribute_name' => $attr->attribute_master_name
                                    ?? $attr->attributeMaster?->name,

                                'attribute_value' => $attr->attribute_value
                                    ?? $attr->attributeValue?->value,

                                'color_code' => $attr->attributeValue?->color_code,
                            ];
                        }
                    );
                },
                []
            ),
        ];
    }
}
