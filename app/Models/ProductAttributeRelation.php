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

    /*
    |--------------------------------------------------------------------------
    | Variant
    |--------------------------------------------------------------------------
    */
    public function variant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Attribute Master
    |--------------------------------------------------------------------------
    */
    public function attributeMaster()
    {
        return $this->belongsTo(
            AttributeMaster::class,
            'attribute_master_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Backward Compatibility Alias
    |--------------------------------------------------------------------------
    | This fixes:
    | Call to undefined relationship [attribute]
    */
    public function attribute()
    {
        return $this->belongsTo(
            AttributeMaster::class,
            'attribute_master_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Attribute Value
    |--------------------------------------------------------------------------
    */
    public function attributeValue()
    {
        return $this->belongsTo(
            AttributeValue::class,
            'attribute_value_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Product Accessor
    |--------------------------------------------------------------------------
    */
    public function getProductAttribute()
    {
        return $this->variant?->product;
    }
}
