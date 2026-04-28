<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVendorStock extends Model
{
    protected $fillable = [
        'product_variant_id',
        'business_id',
        'stock'
    ];

    // Variant
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Business (Vendor)
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
