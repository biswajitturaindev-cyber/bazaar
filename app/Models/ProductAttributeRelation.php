<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeRelation extends Model
{
    protected $fillable = [
        'product_variant_id',
        'attribute_master_id',
        'attribute_value_id'
    ];

    // Variant (MAIN RELATION)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function attributeMaster()
    {
        return $this->belongsTo(
            AttributeMaster::class,
            'attribute_master_id'
        );
    }

    // Attribute Value
    public function attributeValue()
    {
        return $this->belongsTo(
            AttributeValue::class,
            'attribute_value_id'
        );
    }

    // OPTIONAL: Access actual product dynamically
    public function getProductAttribute()
    {
        return $this->variant?->product;
        // uses your ProductVariant accessor
    }
}
