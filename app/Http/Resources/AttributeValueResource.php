<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class AttributeValueResource extends JsonResource
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

            'attribute_master' => [
                'id' => $this->attributeMaster
                    ? Hashids::encode($this->attributeMaster->id)
                    : null,

                'name' => $this->attributeMaster?->name,
            ],

            'value' => $this->value,

            'color_code' => $this->color_code,

            'color_preview' => $this->color_code,

            'status' => $this->status,

            'status_label' => $this->status
                ? 'Active'
                : 'Inactive',
        ];
    }
}
