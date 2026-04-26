<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'product_type',
        'quantity',
        'price',
        'total',
        'product_name',
        'image',
        'attribute_hash'
    ];

    protected $casts = [
        'price' => 'float',
        'total' => 'float',
        'quantity' => 'integer',
    ];

    public function attributes()
    {
        return $this->hasMany(CartAttribute::class);
    }

    public function product()
    {
        return $this->morphTo();
    }
}
