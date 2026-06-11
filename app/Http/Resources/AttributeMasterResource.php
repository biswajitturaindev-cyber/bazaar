<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class AttributeMasterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                       => Hashids::encode($this->id),
            'business_category_id'     => Hashids::encode($this->business_category_id),
            'business_sub_category_id' => Hashids::encode($this->business_sub_category_id),
            'name'                     => $this->name,

            'values' => AttributeValueResource::collection(
                $this->whenLoaded('values')
            ),

            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ];
    }
}
