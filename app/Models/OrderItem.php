<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'business_id',
        'business_category_id',
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

    protected $casts = [
        'product_snapshot' => 'array',
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
            OrderItemAttribute::class
        );
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function businessCategory()
    {
        return $this->belongsTo(
            BusinessCategory::class
        );
    }
}
