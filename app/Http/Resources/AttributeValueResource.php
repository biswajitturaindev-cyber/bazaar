<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' => $this->id,

            'attribute' => [
                'id' => $this->attribute?->id,
                'name' => $this->attribute?->name,
            ],

            'value' => $this->value,
            'color_code' => $this->color_code,

            // useful for frontend
            'color_preview' => $this->color_code
                ? $this->color_code
                : null,

            'status' => $this->status,
            'status_label' => $this->status ? 'Active' : 'Inactive',
        ];
    }
}
