<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_no',
        'user_id',
        'sub_total',
        'handling_charge',
        'delivery_charge',
        'grand_total',
        'total_items',
        'status'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
