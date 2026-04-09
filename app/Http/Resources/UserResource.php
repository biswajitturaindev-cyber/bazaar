<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class UserResource extends JsonResource
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
            'vendor_id' => $this->vendor_id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'status' => $this->status,
            'wallet1' => $this->wallet1,
            'wallet2' => $this->wallet2,
            'wallet3' => $this->wallet3,

            'kyc_status' => $this->statusLabel($this->kyc_status),

            'business' => new BusinessResource(
                $this->whenLoaded('business')
            ),
        ];
    }

    /**
     * Status Label Helper
     */
    private function statusLabel($status)
    {
        return match ((int) $status) {
            1 => 'approved',
            2 => 'pending',
            3 => 'cancel',
            default => 'not submitted',
        };
    }
}
