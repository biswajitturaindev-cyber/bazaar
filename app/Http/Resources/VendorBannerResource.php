<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

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
            'id' => Hashids::encode($this->id),
            'business_id' => Hashids::encode($this->business_id),
            'banner_type' => $this->banner_type,
            'title' => $this->title,
            'image' => $this->image ? asset($this->image): null,
            'status' => $this->status,
            'status_label' => $this->status == 1 ? 'Active' : 'Inactive',
            'sort_order' => $this->sort_order,
        ];
    }
}
