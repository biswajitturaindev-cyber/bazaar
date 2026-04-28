<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantMeta extends Model
{
    protected $fillable = [
        'product_variant_id',
        'meta_title',
        'meta_keyword',
        'meta_description'
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
