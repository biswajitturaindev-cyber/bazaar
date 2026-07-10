<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepositResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'business_id'       => $this->business_id,
            'company_account_id'=> $this->company_account_id,
            'amount'            => (float) $this->amount,
            'payment_method'    => $this->payment_method,
            'transaction_id'    => $this->transaction_id,
            'ref_id'            => $this->ref_id,
            'payment_proof'     => $this->payment_proof ? asset('storage/'.$this->payment_proof) : null,
            'user_note'         => $this->user_note,
            'status'            => $this->status,
            'status_label'      => $this->status_label,
            'created_at'        => $this->created_at->format('d M Y h:i A'),
        ];
    }
}
