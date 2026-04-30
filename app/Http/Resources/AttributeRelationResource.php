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
            'attribute_id' => Hashids::encode($this->attribute_id),
            'attribute_name' => $this->attribute->name ?? null,

            'value_id' => Hashids::encode($this->attribute_value_id),
            'value' => $this->attributeValue->value ?? null,
        ];
    }
}
