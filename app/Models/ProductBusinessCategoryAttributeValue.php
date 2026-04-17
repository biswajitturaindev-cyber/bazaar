<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBusinessCategoryAttributeValue extends Model
{
    protected $table = 'product_category_attribute_values';

    protected $fillable = [
        'product_id',
        'product_type', // from morphs()
        'attribute_id',
        'attribute_value_id',
        'stock',
        'price',
    ];

    /**
     *  Polymorphic relation to Product tables
     */
    public function product()
    {
        return $this->morphTo();
    }

    /**
     * Attribute relation
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    /**
     * Attribute Value relation
     */
    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }
}
