<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class AttributeResource extends JsonResource
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
            'name' => $this->name,
            'status' => $this->status,
            'status_label' => $this->status ? 'Active' : 'Inactive',

            // include values
            'values' => AttributeValueResource::collection($this->whenLoaded('values')),
        ];
    }
}
