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
use Illuminate\Support\Facades\Crypt;

class CartController extends Controller
{
    /**
     * Get cart items
     */
    public function index(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required',
            ]);

            $userId = decodeIdOrFail(
                $request->user_id,
                'Invalid user ID'
            );

            $cart = Cart::with([
                'user',
                'cartAttributes',
                'cartAttributes.attribute',
                'cartAttributes.attributeValue',
            ])
            ->where('user_id', $userId)
            ->latest()
            ->get();

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

            $request->validate([
                'user_id'               => 'required',
                'business_category_id' => 'required',
                'product_id'           => 'required',
                'product_variant_id'   => 'nullable',
                'quantity'             => 'required|integer|min:1',
                'attributes'           => 'required|array|min:1',
            ]);

            // $userId = decodeIdOrFail(
            //     $request->user_id,
            //     'Invalid user ID'
            // );

            $userId = Crypt::decryptString($request->user_id);

            $businessCategoryId = decodeIdOrFail(
                $request->business_category_id,
                'Invalid business category ID'
            );

            $productId = decodeIdOrFail(
                $request->product_id,
                'Invalid product ID'
            );

            $variantId = null;

            if ($request->filled('product_variant_id')) {

                $variantId = decodeIdOrFail(
                    $request->product_variant_id,
                    'Invalid product variant ID'
                );
            }

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

            $attributeHash = md5(
                serialize($decodedAttributes)
            );

            $cartItem = Cart::where([
                'user_id'              => $userId,
                'business_category_id' => $businessCategoryId,
                'product_id'           => $productId,
                'product_variant_id'   => $variantId,
                'attribute_hash'       => $attributeHash,
            ])->first();

            if ($cartItem) {

                $cartItem->increment(
                    'quantity',
                    $request->quantity
                );

            } else {

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

                if (
                    (int) $value->attribute_id !==
                    (int) $attribute->id
                ) {
                    continue;
                }

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
        try {

            /*
            |--------------------------------------------------------------------------
            | Validate Request
            |--------------------------------------------------------------------------
            */
            $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Decode Cart ID
            |--------------------------------------------------------------------------
            */
            $cartId = decodeIdOrFail(
                $id,
                'Invalid cart ID'
            );

            /*
            |--------------------------------------------------------------------------
            | Find Cart Item
            |--------------------------------------------------------------------------
            */
            $cartItem = Cart::with([
                    'user',
                    'productVariant',
                    'productVariant.stocks',
                    'cartAttributes',
                    'cartAttributes.attribute',
                    'cartAttributes.attributeValue',
                ])
                ->where('id', $cartId)
                ->first();

            /*
            |--------------------------------------------------------------------------
            | Cart Not Found
            |--------------------------------------------------------------------------
            */
            if (!$cartItem) {

                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found',
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | Available Stock
            |--------------------------------------------------------------------------
            */
            $availableStock = $cartItem->productVariant
                ? $cartItem->productVariant->stocks->sum('stock')
                : 0;

            /*
            |--------------------------------------------------------------------------
            | Check Stock
            |--------------------------------------------------------------------------
            */
            if ($request->quantity > $availableStock) {

                return response()->json([
                    'success' => false,
                    'message' => 'Only ' . $availableStock . ' items available in stock',
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Update Quantity
            |--------------------------------------------------------------------------
            */
            $cartItem->quantity = $request->quantity;

            $cartItem->save();

            /*
            |--------------------------------------------------------------------------
            | Reload Fresh Data
            |--------------------------------------------------------------------------
            */
            $cartItem->load([
                'user',
                'productVariant',
                'productVariant.stocks',
                'cartAttributes',
                'cartAttributes.attribute',
                'cartAttributes.attributeValue',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */
            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'data' => new CartResource($cartItem),
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Remove item
     */
    public function destroy($id)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | Decode Cart ID
            |--------------------------------------------------------------------------
            */
            $cartId = decodeIdOrFail(
                $id,
                'Invalid cart ID'
            );

            /*
            |--------------------------------------------------------------------------
            | Find Cart Item
            |--------------------------------------------------------------------------
            */
            $cartItem = Cart::find($cartId);

            /*
            |--------------------------------------------------------------------------
            | Cart Item Not Found
            |--------------------------------------------------------------------------
            */
            if (!$cartItem) {

                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found',
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | Delete Cart Attributes
            |--------------------------------------------------------------------------
            */
            $cartItem->cartAttributes()->delete();

            /*
            |--------------------------------------------------------------------------
            | Delete Cart Item
            |--------------------------------------------------------------------------
            */
            $cartItem->delete();

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart successfully',
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
