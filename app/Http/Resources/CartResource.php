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
        /*
        |--------------------------------------------------------------------------
        | Resolve Product
        |--------------------------------------------------------------------------
        */

        $product = $this->product();

        /*
        |--------------------------------------------------------------------------
        | Resolve Variant
        |--------------------------------------------------------------------------
        */

        $variant = null;

        if (!empty($this->product_variant_id)) {

            $variant = ProductVariant::find(
                $this->product_variant_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Product Image
        |--------------------------------------------------------------------------
        */

        $productImage = ProductImage::query()

            ->where(
                'business_category_id',
                $this->business_category_id
            )

            ->where(
                'product_id',
                $this->product_id
            )

            ->when(
                !empty($this->product_variant_id),

                function ($query) {

                    $query->where(
                        'product_variant_id',
                        $this->product_variant_id
                    );
                }
            )

            ->latest('id')

            ->first();

        /*
        |--------------------------------------------------------------------------
        | Image URL
        |--------------------------------------------------------------------------
        */

        $imageUrl = null;

        if (
            $productImage &&
            !empty($productImage->image_medium)
        ) {
            $imageUrl = asset(
                'storage/' . $productImage->image_medium
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Final Price
        |--------------------------------------------------------------------------
        */

        $finalPrice = (float) (
            $variant->final_price ?? 0
        );

        return [

            /*
            |--------------------------------------------------------------------------
            | Cart Info
            |--------------------------------------------------------------------------
            */

            'id' => Hashids::encode(
                $this->id
            ),

            /*
            |--------------------------------------------------------------------------
            | User Details
            |--------------------------------------------------------------------------
            */

            'user' => [

                'user_id' => $this->user
                    ? Hashids::encode($this->user->id)
                    : null,

                'name' => $this->user->name ?? null,

                'email' => $this->user->email ?? null,

                'mobile' => $this->user->mobile ?? null,

                'profile_image' => !empty($this->user?->image)
                    ? asset('storage/' . $this->user->image)
                    : null,
            ],

            /*
            |--------------------------------------------------------------------------
            | Product Details
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | Selected Attributes
            |--------------------------------------------------------------------------
            */

            'attributes' => $this->whenLoaded(
                'cartAttributes',

                function () {

                    return $this->cartAttributes->map(
                        function ($attr) {

                            return [

                                'attribute_id' => Hashids::encode(
                                    $attr->attribute_id
                                ),

                                'attribute_value_id' => Hashids::encode(
                                    $attr->attribute_value_id
                                ),

                                'attribute_name' => $attr->attribute_name,

                                'attribute_value' => $attr->attribute_value,
                            ];
                        }
                    );
                },

                []
            ),
        ];
    }
}
