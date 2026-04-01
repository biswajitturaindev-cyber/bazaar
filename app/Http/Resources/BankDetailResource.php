<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_id' => $this->business_id,
            'account_holder_name' => $this->account_holder_name,
            'bank_name' => $this->bank_name,
            'account_number' => $this->account_number,
            'ifsc_code' => $this->ifsc_code,
            'upi_id' => $this->upi_id,

            'cancelled_cheque' => $this->cancelled_cheque
                ? asset('storage/' . $this->cancelled_cheque)
                : null,

            'created_at' => $this->created_at,
        ];
    }
}
