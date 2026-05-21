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
            'id' => Hashids::encode($this->id),

            'order_no' => $this->order_no,

            /*
            |--------------------------------------------------------------------------
            | Order Status
            |--------------------------------------------------------------------------
            */

            'status' => $this->order_status,

            'status_label' => $this->order_status_text,

            /*
            |--------------------------------------------------------------------------
            | Amounts
            |--------------------------------------------------------------------------
            */

            'sub_total' => (float) $this->items_total,

            'handling_charge' => (float) $this->platform_charge,

            'delivery_charge' => (float) $this->delivery_charge,

            'grand_total' => (float) $this->grand_total,

            /*
            |--------------------------------------------------------------------------
            | Items
            |--------------------------------------------------------------------------
            */

            'total_items' => (int) $this->total_items,

            'items' => $this->whenLoaded('items', function () {

                return $this->items->map(function ($item) {

                    return [

                        'id' => Hashids::encode($item->id),

                        'product_name' => $item->product_name,

                        /*
                        |--------------------------------------------------------------------------
                        | Product Image
                        |--------------------------------------------------------------------------
                        */

                        'image' => $item->product_snapshot['image'] ?? null,

                        /*
                        |--------------------------------------------------------------------------
                        | Pricing
                        |--------------------------------------------------------------------------
                        */

                        'quantity' => (int) $item->quantity,

                        'price' => (float) $item->final_price,

                        'total' => (float) $item->subtotal,

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
                                    'name' => $attr->attribute_name,

                                    'value' => $attr->attribute_value,
                                ];
                            }),
                    ];
                });
            }),

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            'addresses' => $this->whenLoaded('addresses'),

            /*
            |--------------------------------------------------------------------------
            | Status History
            |--------------------------------------------------------------------------
            */

            'status_histories' => $this->whenLoaded('statusHistories'),

            /*
            |--------------------------------------------------------------------------
            | Date
            |--------------------------------------------------------------------------
            */

            'created_at' => optional(
                $this->created_at
            )->format('Y-m-d H:i:s'),
        ];
    }
}
