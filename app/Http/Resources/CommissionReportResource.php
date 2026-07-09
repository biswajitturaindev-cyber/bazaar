<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class CommissionReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $items = $this->items;

        $totalItems = $items->count();

        $orderAmount = $items->sum('subtotal');

        $commissionAmount = $items->sum(function ($item) {
            return ($item->subtotal * $item->product_commission) / 100;
        });

        return [
            'order_id' => Hashids::encode($this->id),
            'invoice_no' => $this->invoice_no,
            'invoice_date' => optional($this->created_at)->format('d-m-Y'),
            'total_items' => $totalItems,
            'order_amount' => round($orderAmount, 2),

            // Average commission percentage
            'commission_percent' => round($items->avg('product_commission') ?? 0, 2),

            'commission_amount' => round($commissionAmount, 2),
        ];
    }
}
