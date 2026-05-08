<?php

namespace App\Http\Controllers\Api\v1\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Vinkla\Hashids\Facades\Hashids;
use App\Models\CartAttribute;

class CartController extends Controller
{
    /**
     * Get cart items
     */
    public function index(Request $request)
    {
        try {
            /*
            |--------------------------------------------------------------------------
            | Validate Request
            |--------------------------------------------------------------------------
            */
            $request->validate([
                'user_id' => 'required',
            ]);
            /*
            |--------------------------------------------------------------------------
            | Decode User ID
            |--------------------------------------------------------------------------
            */
            $userId = decodeIdOrFail(
                $request->user_id,
                'Invalid user ID'
            );

            /*
            |--------------------------------------------------------------------------
            | Get Cart Items
            |--------------------------------------------------------------------------
            */
            $cart = Cart::with([
                'user',
                'cartAttributes',
                'cartAttributes.attribute',
                'cartAttributes.attributeValue',
            ])
            ->where('user_id', $userId)
            ->latest()
            ->get();
            /*
            |--------------------------------------------------------------------------
            | Response
            |--------------------------------------------------------------------------
            */
            return response()->json([
                'success' => true,
                'message' => 'Cart fetched successfully',
                'total_items' => $cart->count(),
                'data' => CartResource::collection($cart),
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Validation
            |--------------------------------------------------------------------------
            */

            $request->validate([
                'user_id'               => 'required',
                'business_category_id' => 'required',
                'product_id'           => 'required',
                'product_variant_id'   => 'nullable',
                'quantity'             => 'required|integer|min:1',
                'attributes'           => 'required|array|min:1',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Decode IDs
            |--------------------------------------------------------------------------
            */

            $userId = decodeIdOrFail(
                $request->user_id,
                'Invalid user ID'
            );

            $businessCategoryId = decodeIdOrFail(
                $request->business_category_id,
                'Invalid business category ID'
            );

            $productId = decodeIdOrFail(
                $request->product_id,
                'Invalid product ID'
            );

            /*
            |--------------------------------------------------------------------------
            | Decode Variant ID
            |--------------------------------------------------------------------------
            */

            $variantId = null;

            if ($request->filled('product_variant_id')) {

                $variantId = decodeIdOrFail(
                    $request->product_variant_id,
                    'Invalid product variant ID'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Resolve Product Model
            |--------------------------------------------------------------------------
            */

            $modelClass = config('product.model_map')[
                $businessCategoryId
            ] ?? null;

            if (!$modelClass) {

                throw new \Exception(
                    'Invalid business category'
                );
            }

            $product = $modelClass::find($productId);

            if (!$product) {

                throw new \Exception(
                    'Product not found'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Normalize Attributes
            |--------------------------------------------------------------------------
            */

            $decodedAttributes = collect(
                $request->input('attributes', [])
            )
            ->map(function ($attr) {

                return [

                    'attribute_id' => decodeIdOrFail(
                        $attr['attribute_id'],
                        'Invalid attribute ID'
                    ),

                    'attribute_value_id' => decodeIdOrFail(
                        $attr['attribute_value_id'],
                        'Invalid attribute value ID'
                    ),
                ];
            })
            ->sortBy('attribute_id')
            ->values()
            ->toArray();

            /*
            |--------------------------------------------------------------------------
            | Generate Unique Attribute Hash
            |--------------------------------------------------------------------------
            */

            $attributeHash = md5(
                serialize($decodedAttributes)
            );

            /*
            |--------------------------------------------------------------------------
            | Find Existing Cart
            |--------------------------------------------------------------------------
            */

            $cartItem = Cart::where([

                'user_id'              => $userId,

                'business_category_id' => $businessCategoryId,

                'product_id'           => $productId,

                'product_variant_id'   => $variantId,

                'attribute_hash'       => $attributeHash,

            ])->first();

            /*
            |--------------------------------------------------------------------------
            | Update Quantity
            |--------------------------------------------------------------------------
            */

            if ($cartItem) {

                $cartItem->increment(
                    'quantity',
                    $request->quantity
                );

            } else {

                /*
                |--------------------------------------------------------------------------
                | Create Cart Item
                |--------------------------------------------------------------------------
                */

                $cartItem = Cart::create([

                    'user_id'              => $userId,

                    'business_category_id' => $businessCategoryId,

                    'product_id'           => $productId,

                    'product_variant_id'   => $variantId,

                    'quantity'             => $request->quantity,

                    'product_name'         => $product->name ?? null,

                    'attribute_hash'       => $attributeHash,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Store Cart Attributes
            |--------------------------------------------------------------------------
            */

            foreach ($decodedAttributes as $attr) {

                $attribute = Attribute::find(
                    $attr['attribute_id']
                );

                $value = AttributeValue::find(
                    $attr['attribute_value_id']
                );

                if (!$attribute || !$value) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Attribute Mapping
                |--------------------------------------------------------------------------
                */

                if (
                    (int) $value->attribute_id !==
                    (int) $attribute->id
                ) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Insert / Update Cart Attributes
                |--------------------------------------------------------------------------
                */

                CartAttribute::updateOrCreate(

                    [
                        'cart_id' => $cartItem->id,

                        'attribute_id' => $attribute->id,
                    ],

                    [
                        'attribute_value_id' => $value->id,

                        'attribute_name' => $attribute->name,

                        'attribute_value' => $value->value,
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Response
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => true,

                'message' => 'Product added to cart',

                'data' => new CartResource(
                    $cartItem->load('cartAttributes')
                )
            ]);

        } catch (\Throwable $e) {

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),

                'line' => $e->getLine(),

            ], 500);
        }
    }

    /**
     * Update quantity
     */
    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'user_id' => 'required',
        //     'quantity' => 'required|integer|min:1'
        // ]);

        // $userId = decodeIdOrFail($request->user_id, 'Invalid user ID');
        // $cartId = decodeIdOrFail($id, 'Invalid cart ID');

        // $cartItem = Cart::where('user_id', $userId)
        //     ->where('id', $cartId)
        //     ->firstOrFail();

        // $cartItem->quantity = $request->quantity;
        // $cartItem->total = $cartItem->price * $request->quantity;
        // $cartItem->save();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Cart updated',
        //     'data'    => new CartResource($cartItem)
        // ]);
    }

    /**
     * Remove item
     */
    public function destroy(Request $request, $id)
    {
        // $userId = decodeIdOrFail($request->user_id, 'Invalid user ID');
        // $cartId = decodeIdOrFail($id, 'Invalid cart ID');

        // $cartItem = Cart::where('user_id', $userId)
        //     ->where('id', $cartId)
        //     ->firstOrFail();

        // $cartItem->delete();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Item removed from cart'
        // ]);
    }
}
