<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class HsnResource extends JsonResource
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
            'hsn_code' => $this->hsn_code,
            'description' => $this->description,
            'cgst' => $this->cgst,
            'sgst' => $this->sgst,
            'igst' => $this->igst,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
