<?php

namespace App\Http\Controllers\Api\v1\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Vinkla\Hashids\Facades\Hashids;

class CartController extends Controller
{
    /**
     * Get cart items
     */
    public function index(Request $request)
    {
        $userId = decodeIdOrFail($request->user_id, 'Invalid user ID');

        $cart = Cart::with([
            'attributes.attribute',
            'attributes.attributeValue'
        ])
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => CartResource::collection($cart)
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'user_id'      => 'required',
                'product_id'   => 'required',
                'product_type' => 'required|string',
                'quantity'     => 'required|integer|min:1',
                'attributes'   => 'required',
            ]);

            // ✅ Decode IDs
            $userId = decodeIdOrFail($request->user_id, 'Invalid user ID');
            $productId = decodeIdOrFail($request->product_id, 'Invalid product ID');

            // ✅ Resolve model
            $modelClass = "App\\Models\\" . $request->product_type;

            if (!class_exists($modelClass)) {
                throw new \Exception('Invalid product type');
            }

            $product = $modelClass::find($productId);

            if (!$product) {
                throw new \Exception('Product not found');
            }

            // ✅ FIX: Handle form-data & JSON both
            $attributesInput = $request->input('attributes', []);

            if (!is_array($attributesInput) || empty($attributesInput)) {
                throw new \Exception('Attributes must be a non-empty array');
            }

            // Normalize array (important for form-data)
            $decodedAttributes = collect($attributesInput)->map(function ($attr) {

                if (!is_array($attr)) {
                    throw new \Exception('Invalid attribute format');
                }

                if (!isset($attr['attribute_id'], $attr['attribute_value_id'])) {
                    throw new \Exception('Invalid attribute format');
                }

                return [
                    'attribute_id' => decodeIdOrFail($attr['attribute_id'], 'Invalid attribute ID'),
                    'attribute_value_id' => decodeIdOrFail($attr['attribute_value_id'], 'Invalid attribute value ID'),
                ];
            })->values()->toArray();

            if (empty($decodedAttributes)) {
                throw new \Exception('Attributes decoding failed');
            }

            // ✅ Generate hash
            $attributeHash = md5(json_encode($decodedAttributes));

            // ✅ Check existing cart item
            $cartItem = Cart::where([
                'user_id'        => $userId,
                'product_id'     => $productId,
                'product_type'   => $request->product_type,
                'attribute_hash' => $attributeHash
            ])->first();

            if ($cartItem) {

                // 🔄 Update quantity
                $cartItem->quantity += $request->quantity;
                $cartItem->total = $cartItem->price * $cartItem->quantity;
                $cartItem->save();
            } else {

                // ✅ Create cart
                $cartItem = Cart::create([
                    'user_id'        => $userId,
                    'product_id'     => $productId,
                    'product_type'   => $request->product_type,
                    'quantity'       => $request->quantity,
                    'price'          => $product->final_price ?? $product->price ?? 0,
                    'total'          => ($product->final_price ?? $product->price ?? 0) * $request->quantity,
                    'product_name'   => $product->name ?? null,
                    'image'          => $product->image ?? null,
                    'attribute_hash' => $attributeHash
                ]);

                // ✅ Insert attributes
                foreach ($decodedAttributes as $attr) {

                    $attribute = Attribute::find($attr['attribute_id']);
                    $value = AttributeValue::find($attr['attribute_value_id']);

                    if (!$attribute || !$value) {
                        throw new \Exception('Attribute not found');
                    }

                    // Validate mapping
                    if ($value->attribute_id != $attribute->id) {
                        throw new \Exception('Invalid attribute selection');
                    }

                    $cartItem->attributes()->create([
                        'attribute_id'        => $attribute->id,
                        'attribute_value_id'  => $value->id,
                        'attribute_name'      => $attribute->name,
                        'attribute_value'     => $value->value,
                        'price'               => $value->price ?? 0
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'data'    => new CartResource($cartItem->load('attributes'))
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine()
            ], 500);
        }
    }

    /**
     * Update quantity
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        $userId = decodeIdOrFail($request->user_id, 'Invalid user ID');
        $cartId = decodeIdOrFail($id, 'Invalid cart ID');

        $cartItem = Cart::where('user_id', $userId)
            ->where('id', $cartId)
            ->firstOrFail();

        $cartItem->quantity = $request->quantity;
        $cartItem->total = $cartItem->price * $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'data'    => new CartResource($cartItem)
        ]);
    }

    /**
     * Remove item
     */
    public function destroy(Request $request, $id)
    {
        $userId = decodeIdOrFail($request->user_id, 'Invalid user ID');
        $cartId = decodeIdOrFail($id, 'Invalid cart ID');

        $cartItem = Cart::where('user_id', $userId)
            ->where('id', $cartId)
            ->firstOrFail();

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart'
        ]);
    }
}
