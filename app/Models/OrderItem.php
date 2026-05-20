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
        'quantity'          => 'integer',

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
        return $this->belongsTo(
            Order::class,
            'order_id'
        );
    }

    public function variant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }

    public function attributes()
    {
        return $this->hasMany(
            OrderItemAttribute::class,
            'order_item_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getTotalAttribute()
    {
        return (
            $this->final_price
            * $this->quantity
        );
    }

    public function getProductAttributeDataAttribute()
    {
        return $this->variant?->attributes ?? [];
    }
}
