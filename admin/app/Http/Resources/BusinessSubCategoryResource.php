<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class BusinessSubCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => Hashids::encode($this->id),
            'name' => $this->name,
            'image' => $this->image
                ? asset('storage/business_sub_category/' . $this->image)
                : null,
            'status' => $this->status,
            'commission' => $this->commission,
            'status_label' => $this->status == 1 ? 'Active' : 'Inactive',
            // Parent Category Info
            'category' => [
                'id' => Hashids::encode($this->category?->id),
                'name' => $this->category?->name,
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
