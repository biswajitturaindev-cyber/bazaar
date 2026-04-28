<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeRelation extends Model
{
    protected $fillable = [
        'product_variant_id',
        'product_id',
        'attribute_id',
        'attribute_value_id'
    ];

    // Variant
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    // Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Attribute
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    // Attribute Value
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
