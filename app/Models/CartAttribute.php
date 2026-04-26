<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartAttribute extends Model
{
    protected $fillable = [
        'cart_id',
        'attribute_id',
        'attribute_value_id',
        'attribute_name',
        'attribute_value',
        'price'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
