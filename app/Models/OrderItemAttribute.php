<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemAttribute extends Model
{
    protected $fillable = [
        'order_item_id',
        'attribute_master_id',
        'attribute_value_id',
        'attribute_name',
        'attribute_value',
    ];

    protected $casts = [
        'order_item_id' => 'integer',
        'attribute_master_id' => 'integer',
        'attribute_value_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function orderItem()
    {
        return $this->belongsTo(
            OrderItem::class,
            'order_item_id'
        );
    }

    public function attributeMaster()
    {
        return $this->belongsTo(
            AttributeMaster::class,
            'attribute_master_id'
        );
    }

    public function attributeValue()
    {
        return $this->belongsTo(
            AttributeValue::class,
            'attribute_value_id'
        );
    }
}
