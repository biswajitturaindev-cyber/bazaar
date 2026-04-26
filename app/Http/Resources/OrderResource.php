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

            'status' => $this->status,
            'status_label' => match ($this->status) {
                'pending' => 'Pending',
                'processing' => 'Processing',
                'delivered' => 'Delivered',
                'cancelled' => 'Cancelled',
                default => 'Unknown'
            },

            'sub_total' => (float) $this->sub_total,
            'handling_charge' => (float) $this->handling_charge,
            'delivery_charge' => (float) $this->delivery_charge,
            'grand_total' => (float) $this->grand_total,

            'total_items' => $this->total_items,

            'items' => $this->items->map(function ($item) {
                return [
                    'product_name' => $item->product_name,

                    // FULL IMAGE URL
                    'image' => $item->image
                        ? asset('storage/products/' . $item->image)
                        : null,

                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                    'total' => (float) $item->total,

                    'attributes' => $item->attributes->map(function ($attr) {
                        return [
                            'name' => $attr->attribute_name,
                            'value' => $attr->attribute_value,
                            'price' => (float) $attr->price,
                        ];
                    })
                ];
            }),

            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s')
        ];
    }
}
