<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class ProductAttributeValue extends Model
{
    protected $fillable = [
        'product_id',
        'attribute_id',
        'attribute_value_id',
    ];

    // Relationships
    protected $appends = ['id'];

    public function getIdAttribute()
    {
        return Hashids::encode($this->attributes['id']);
    }


    // Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Attribute (e.g. Size, Color)
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    // Attribute Value (e.g. Large, Red)
    public function value()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }
}
