<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_no',
        'user_id',
        'billing_address_id',
        'shipping_address_id',
        'total_items',
        'items_total',
        'discount_amount',
        'platform_charge',
        'delivery_charge',
        'tax_amount',
        'grand_total',
        'loyalty_used',
        'loyalty_earned',
        'wallet_used',
        'online_paid',
        'payment_status',
        'payment_method',
        'order_status',
        'notes',
        'placed_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function billingAddress()
    {
        return $this->belongsTo(
            UserAddress::class,
            'billing_address_id'
        );
    }

    public function shippingAddress()
    {
        return $this->belongsTo(
            UserAddress::class,
            'shipping_address_id'
        );
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(
            OrderStatusHistory::class
        );
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
}
