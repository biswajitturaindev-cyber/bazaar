<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'business_category_id',
        'product_id',
        'product_variant_id',
        'image_large',
        'image_medium',
        'image_small'
    ];

    // Optional: hide internal fields from API
    protected $hidden = [
        'business_category_id',
        'product_id',
        'product_variant_id',
        'created_at',
        'updated_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // Main relation (CORRECT)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    // Access real product via variant (dynamic tables supported)
    public function getProductAttribute()
    {
        return $this->variant?->product;
    }

    // Optional: return image URLs directly
    public function getUrlsAttribute()
    {
        return [
            'large' => $this->image_large ? asset('storage/' . $this->image_large) : null,
            'medium' => $this->image_medium ? asset('storage/' . $this->image_medium) : null,
            'small' => $this->image_small ? asset('storage/' . $this->image_small) : null,
        ];
    }
}
