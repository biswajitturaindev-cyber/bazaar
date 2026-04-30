<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeRelation extends Model
{
        protected $fillable = [
        'product_variant_id',
        'attribute_id',
        'attribute_value_id'
    ];

    // ✅ Variant (MAIN RELATION)
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // ✅ Attribute
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    // ✅ Attribute Value
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }

    // 🔥 OPTIONAL: Access actual product dynamically
    public function getProductAttribute()
    {
        return $this->variant?->product;
        // uses your ProductVariant accessor
    }
}
