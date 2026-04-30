<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
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

            'image_large' => $this->image_large
                ? asset('storage/' . $this->image_large)
                : null,

            'image_medium' => $this->image_medium
                ? asset('storage/' . $this->image_medium)
                : null,

            'image_small' => $this->image_small
                ? asset('storage/' . $this->image_small)
                : null,
        ];
    }
}
