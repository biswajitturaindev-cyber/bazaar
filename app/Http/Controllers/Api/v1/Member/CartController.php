<?php

namespace App\Http\Controllers\Api\v1\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\AttributeMaster;
use App\Models\AttributeValue;
use Vinkla\Hashids\Facades\Hashids;
use App\Models\CartAttribute;
use Illuminate\Support\Facades\Crypt;

class CartController extends Controller
{
    /**
     * Get cart items
     */
    // public function index(Request $request)
    // {

    //     try {

    //         $request->validate([
    //             'user_id' => 'required',
    //         ]);

    //         $userId = $request->user_id;

    //         $cart = Cart::where('user_id', $userId)
    //             ->latest('id')
    //             ->get();

    //         $kyc = Cart::leftJoin('kyc_details', 'kyc_details.business_id', '=', 'carts.business_id')
    //             ->where('carts.user_id', $userId)
    //             ->select(
    //                 'kyc_details.gst_no',
    //                 'kyc_details.gst_state_code',
    //                 'kyc_details.gst_address'
    //             )
    //             ->first();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Cart fetched successfully',
    //             'total_items' => $cart->count(),
    //             'vendor_gst_details' => $kyc,
    //             'data' => CartResource::collection($cart),
    //         ]);

    //     } catch (\Throwable $e) {

    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //             'line' => $e->getLine(),
    //         ], 500);
    //     }
    // }

    public function index(Request $request)
    {
        try {

            $request->validate([
                'user_id' => 'required',
            ]);

            $userId = $request->user_id;

            $cart = Cart::with([

                'business:id,business_name',

                'productVariant' => function ($q) {
                    $q->select([
                        'id',
                        'product_id',
                        'product_type',
                        'sku',
                        'barcode',
                        'mrp',
                        'cost_price',
                        'selling_price',
                        'discount',
                        'final_price',
                        'is_primary',
                    ]);
                },

                'cartAttributes' => function ($q) {
                    $q->with([
                        'attributeMaster:id,name',
                        'attributeValue:id,value,color_code',
                    ]);
                },

                'kycDetail:id,business_id,gst_no,gst_state_code,gst_address',

            ])
            ->where('user_id', $userId)
            ->latest('id')
            ->get();

            return response()->json([
                'success' => true,
                'message' => 'Cart fetched successfully',
                'total_items' => $cart->count(),
                'data' => CartResource::collection($cart),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {

            \Log::error('Cart Fetch Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
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

                'user_id' => 'required|integer',
                'business_id' => 'required',
                'business_category_id' => 'required',
                'product_id' => 'required',
                'product_variant_id' => 'nullable',
                'quantity' => 'required|integer|min:1',

                'attributes' => 'nullable|array',

                'attributes.*.attribute_master_id' => 'required_with:attributes',
                'attributes.*.attribute_value_id' => 'required_with:attributes',
            ]);

            $userId = $request->user_id;

            $businessId = decodeIdOrFail(
                $request->business_id,
                'Invalid business ID'
            );

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


            $existingBusinessId = Cart::where('user_id', $userId)
                ->value('business_id');

            if (
                $existingBusinessId &&
                (int) $existingBusinessId !== (int) $businessId
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart contains products from another business. Please clear your cart before adding products from a different business.',
                    'action_required' => 'clear_cart'
                ], 422);
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

                    'attribute_master_id' => decodeIdOrFail(
                        $attr['attribute_master_id'],
                        'Invalid attribute ID'
                    ),

                    'attribute_value_id' => decodeIdOrFail(
                        $attr['attribute_value_id'],
                        'Invalid attribute value ID'
                    ),
                ];
            })
            ->sortBy('attribute_master_id')
            ->values()
            ->toArray();

            $attributeHash = md5(
                serialize($decodedAttributes)
            );

            $cartItem = Cart::where([
                'user_id'              => $userId,
                'business_id'          => $businessId,
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

                $cartItem->refresh();

            } else {

                $cartItem = Cart::create([
                    'user_id'              => $userId,
                    'business_id'          => $businessId,
                    'business_category_id' => $businessCategoryId,
                    'product_id'           => $productId,
                    'product_variant_id'   => $variantId,
                    'quantity'             => $request->quantity,
                    'product_name'         => $product->name ?? null,
                    'attribute_hash'       => $attributeHash,
                ]);
            }

            foreach ($decodedAttributes as $attr) {

                $attributeMaster = AttributeMaster::find(
                    $attr['attribute_master_id']
                );

                $attributeValue = AttributeValue::find(
                    $attr['attribute_value_id']
                );

                if (
                    !$attributeMaster ||
                    !$attributeValue
                ) {
                    continue;
                }

                if (
                    (int) $attributeValue->attribute_id !==
                    (int) $attributeMaster->id
                ) {
                    continue;
                }

                CartAttribute::updateOrCreate(
                    [
                        'cart_id' => $cartItem->id,
                        'attribute_master_id' => $attributeMaster->id,
                    ],
                    [
                        'attribute_value_id' => $attributeValue->id,
                        'attribute_master_name' => $attributeMaster->name,
                        'attribute_value' => $attributeValue->value,
                        'price' => $attributeValue->price ?? null,
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
                'line'    => $e->getLine(),
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
    /**
     * Remove item
     */
    public function deleteByUser($userId)
    {
        try {

            $deleted = Cart::where('user_id', $userId)->delete();

            if (!$deleted) {
                return response()->json([
                    'status' => false,
                    'message' => 'No cart items found for this user'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'User cart deleted successfully'
            ], 200);

        } catch (\Exception $e) {

            \Log::error('Delete User Cart Error: ' . $e->getMessage(), [
                'user_id' => $userId,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete user cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
