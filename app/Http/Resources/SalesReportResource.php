<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class SalesReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $orders = $this->resource;

        $firstOrder = $orders->first();

        $invoiceCount = $orders->count();

        $totalAmount = 0;

        $totalCommission = 0;

        foreach ($orders as $order) {

            foreach ($order->items as $item) {

                $totalAmount += $item->subtotal;

                $commission = $item->commission == 0
                    ? $item->vendor_commission
                    : $item->commission;

                $totalCommission +=
                    ($item->subtotal * $commission) / 100;
            }
        }

        return [

            'member_id' => $firstOrder->user_id,

            'no_of_invoice' => $invoiceCount,

            'total_amount' => number_format($totalAmount, 2, '.', ''),

            'total_commission' => number_format($totalCommission, 2, '.', ''),

        ];
    }
}
