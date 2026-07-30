<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class BannerResource extends JsonResource
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
            'banner_type' => $this->banner_type,
            'banner_type_label' => match ($this->banner_type) {
                'promotional_banner' => 'Promotional Banner',
                'advertisement_banner' => 'Advertisement Banner',
                default => '',
            },
            'title' => $this->title,
            'image' => $this->image
                ? asset('storage/' . $this->image)
                : null,
            'sort_order' => $this->sort_order,
            'status' => (bool) $this->status,
            'status_label' => $this->status ? 'Active' : 'Inactive',
        ];
    }
}
