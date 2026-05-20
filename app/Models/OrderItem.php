<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',

        'product_id',
        'product_variant_id',

        'product_name',
        'sku',

        'quantity',

        'mrp',
        'selling_price',
        'discount_amount',
        'final_price',
        'subtotal',

        'loyalty_points',

        'product_snapshot',
    ];

    /**
     * Attribute Casting
     */
    protected $casts = [
        'mrp'               => 'decimal:2',
        'selling_price'     => 'decimal:2',
        'discount_amount'   => 'decimal:2',
        'final_price'       => 'decimal:2',
        'subtotal'          => 'decimal:2',
        'loyalty_points'    => 'decimal:2',

        'product_snapshot'  => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(
            ProductAttributeValue::class,
            'product_variant_id'
        );
    }

    public function attributes()
    {
        return $this->hasMany(
            OrderItemAttribute::class
        );
    }
}
