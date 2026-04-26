<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_type',
        'product_name',
        'image',
        'quantity',
        'price',
        'total'
    ];

    public function attributes()
    {
        return $this->hasMany(OrderItemAttribute::class);
    }
}
