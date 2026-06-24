<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderAddress extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',

        'phone',

        'billing_address',
        'billing_city_id',
        'billing_state_id',
        'billing_pincode',

        'shipping_address_id',
        'shipping_address',
        'shipping_city_id',
        'shipping_state_id',
        'shipping_pincode',
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

    public function billingCity()
    {
        return $this->belongsTo(
            City::class,
            'billing_city_id'
        );
    }

    public function billingState()
    {
        return $this->belongsTo(
            State::class,
            'billing_state_id'
        );
    }

    public function shippingCity()
    {
        return $this->belongsTo(
            City::class,
            'shipping_city_id'
        );
    }

    public function shippingState()
    {
        return $this->belongsTo(
            State::class,
            'shipping_state_id'
        );
    }
}
