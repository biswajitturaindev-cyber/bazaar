<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorBannerResource extends JsonResource
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
            'banner_type' => $this->banner_type,
            'title' => $this->title,
            'image' => $this->image,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
        ];
    }
}
