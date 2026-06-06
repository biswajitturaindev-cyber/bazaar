<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Basic Information
            |--------------------------------------------------------------------------
            */

            'id' => Hashids::encode($this->id),
            'order_no' => $this->order_no,
            'invoice_no' => $this->invoice_no,
            'business_id' => Hashids::encode($this->business_id),
            'business_category_id' => $this->business_category_id,
            'user_id' => $this->user_id,

            /*
            |--------------------------------------------------------------------------
            | Payment Information
            |--------------------------------------------------------------------------
            */

            'payment_status' => $this->payment_status,
            'payment_status_label' => $this->payment_status_text,
            'payment_method' => $this->payment_method,
            'payment_method_label' => $this->payment_method_text,

            /*
            |--------------------------------------------------------------------------
            | Order Status
            |--------------------------------------------------------------------------
            */

            'order_status' => $this->order_status,
            'order_status_label' => $this->order_status_text,

            /*
            |--------------------------------------------------------------------------
            | Amounts
            |--------------------------------------------------------------------------
            */

            'total_items' => (int) $this->total_items,
            'items_total' => (float) $this->items_total,
            'discount_amount' => (float) $this->discount_amount,
            'platform_charge' => (float) $this->platform_charge,
            'delivery_charge' => (float) $this->delivery_charge,
            'tax_amount' => (float) $this->tax_amount,
            'grand_total' => (float) $this->grand_total,

            /*
            |--------------------------------------------------------------------------
            | Loyalty / Wallet
            |--------------------------------------------------------------------------
            */

            'loyalty_used' => (float) $this->loyalty_used,
            'loyalty_earned' => (float) $this->loyalty_earned,
            'wallet_used' => (float) $this->wallet_used,
            'online_paid' => (float) $this->online_paid,

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'notes' => $this->notes,

            /*
            |--------------------------------------------------------------------------
            | Items
            |--------------------------------------------------------------------------
            */

            'items' => $this->whenLoaded('items', function () {

                return $this->items->map(function ($item) {

                    return [

                        'id' => Hashids::encode($item->id),
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'product_name' => $item->product_name,
                        'sku' => $item->sku,

                        /*
                        |--------------------------------------------------------------------------
                        | Product Image
                        |--------------------------------------------------------------------------
                        */

                        // 'image' => isset($item->product_snapshot['image'])
                        //             ? asset('storage/' . $item->product_snapshot['image'])
                        //             : null,

                        'image' => optional(
                            $item->variant?->images?->first()
                        )->image_medium
                            ? asset(
                                'storage/' .
                                $item->variant->images->first()->image_medium
                            )
                            : null,

                        /*
                        |--------------------------------------------------------------------------
                        | Quantity & Pricing
                        |--------------------------------------------------------------------------
                        */

                        // 'quantity' => (int) $item->quantity,
                        // 'modified_quantity' => (int) $item->modified_quantity,
                        // 'mrp' => (float) $item->mrp,
                        // 'selling_price' => (float) $item->selling_price,
                        // 'discount_amount' => (float) $item->discount_amount,
                        // 'final_price' => (float) $item->final_price,
                        // 'subtotal' => (float) $item->subtotal,
                        // 'loyalty_points' => (float) $item->loyalty_points,


                            'quantity' => (int) $item->quantity,

                            'modified_quantity' => $item->modified_quantity
                                ? (int) $item->modified_quantity
                                : null,

                            'status' => $item->status,

                            'cancel_reason_id' => $item->cancel_reason_id
                                ? Hashids::encode($item->cancel_reason_id)
                                : null,

                            'cancel_note' => $item->cancel_note,

                            'cancelled_by' => $item->cancelled_by,

                            'cancelled_at' => $item->cancelled_at
                                ? $item->cancelled_at->format('Y-m-d H:i:s')
                                : null,

                            'mrp' => (float) $item->mrp,
                            'selling_price' => (float) $item->selling_price,
                            'discount_amount' => (float) $item->discount_amount,
                            'final_price' => (float) $item->final_price,
                            'subtotal' => (float) $item->subtotal,
                            'loyalty_points' => (float) $item->loyalty_points,

                        /*
                        |--------------------------------------------------------------------------
                        | Attributes
                        |--------------------------------------------------------------------------
                        */

                        'attributes' => $item->attributes
                            ->unique(function ($attr) {
                                return $attr->attribute_id . '-' . $attr->attribute_value_id;
                            })
                            ->values()
                            ->map(function ($attr) {

                                return [
                                    'attribute_id' => $attr->attribute_id,
                                    'attribute_value_id' => $attr->attribute_value_id,
                                    'name' => $attr->attribute_name,
                                    'value' => $attr->attribute_value,
                                    'color_code' => $attr->attributeValue?->color_code,
                                ];
                            }),

                        /*
                        |--------------------------------------------------------------------------
                        | Product Snapshot
                        |--------------------------------------------------------------------------
                        */

                        'product_snapshot' => $item->product_snapshot,
                    ];
                });
            }),

            /*
            |--------------------------------------------------------------------------
            | Addresses
            |--------------------------------------------------------------------------
            */

            'addresses' => $this->whenLoaded(
                'addresses'
            ),

            /*
            |--------------------------------------------------------------------------
            | Status Histories
            |--------------------------------------------------------------------------
            */

            'status_histories' => $this->whenLoaded(
                'statusHistories'
            ),

            /*
            |--------------------------------------------------------------------------
            | Business
            |--------------------------------------------------------------------------
            */

            'business' => $this->whenLoaded(
                'business'
            ),

            /*
            |--------------------------------------------------------------------------
            | Business Category
            |--------------------------------------------------------------------------
            */

            'business_category' => $this->whenLoaded(
                'businessCategory'
            ),

            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'placed_at' => optional(
                $this->placed_at
            )->format('Y-m-d H:i:s'),

            'created_at' => optional(
                $this->created_at
            )->format('Y-m-d H:i:s'),

            'updated_at' => optional(
                $this->updated_at
            )->format('Y-m-d H:i:s'),
        ];
    }
}
