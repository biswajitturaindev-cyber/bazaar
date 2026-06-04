<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Vinkla\Hashids\Facades\Hashids;

class MasterProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Basic Details
            |--------------------------------------------------------------------------
            */
            'id' => Hashids::encode($this->id),

            'name' => $this->name,

            /*
            |--------------------------------------------------------------------------
            | Category Details
            |--------------------------------------------------------------------------
            */
            'category' => $this->category?->name,

            'sub_category' => $this->subCategory?->name,

            'sub_sub_category' => $this->subSubCategory?->name,

            /*
            |--------------------------------------------------------------------------
            | HSN
            |--------------------------------------------------------------------------
            */
            'hsn' => $this->hsn?->hsn_code,

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            */
            'product_price' => $this->product_price,

            'selling_price' => $this->selling_price,

            'commission' => $this->commission,
            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */
            'description' => $this->description,

            /*
            |--------------------------------------------------------------------------
            | Primary Image
            |--------------------------------------------------------------------------
            */
            'primary_image' => $this->primaryImage
                ? [
                    'id' => $this->primaryImage->id,
                    'image' => $this->primaryImage->image_url,
                    'is_primary' => (bool) $this->primaryImage->is_primary,
                ]
                : null,

            /*
            |--------------------------------------------------------------------------
            | All Images
            |--------------------------------------------------------------------------
            */
            'images' => $this->images->map(function ($image) {

                return [
                    'id' => $image->id,

                    'image' => $image->image_url,

                    'is_primary' => (bool) $image->is_primary,
                ];
            }),

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */
            'status' => $this->status,

            'status_label' => match ($this->status) {
                1 => 'Active',
                0 => 'Inactive',
                default => 'Unknown',
            },

            /*
            |--------------------------------------------------------------------------
            | Date
            |--------------------------------------------------------------------------
            */
            'created_at' => $this->created_at?->format('d-m-Y'),
        ];
    }
}
