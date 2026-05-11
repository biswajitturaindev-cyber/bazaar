<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemAttribute extends Model
{
    protected $fillable = [
        'order_item_id',
        'attribute_id',
        'attribute_value_id',
        'attribute_name',
        'attribute_value',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function orderItem()
    {
        return $this->belongsTo(
            OrderItem::class
        );
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function attributeValue()
    {
        return $this->belongsTo(
            AttributeValue::class
        );
    }
}
