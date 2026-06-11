<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class AttributeRelationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'attribute_id' => $this->attributeMaster
                ? Hashids::encode($this->attributeMaster->id)
                : null,

            'attribute_name' => $this->attributeMaster?->name,

            'value_id' => $this->attributeValue
                ? Hashids::encode($this->attributeValue->id)
                : null,

            'value' => $this->attributeValue?->value,

            'color_code' => $this->attributeValue?->color_code,
        ];
    }
}
