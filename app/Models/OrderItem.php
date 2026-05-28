<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Item Status
    |--------------------------------------------------------------------------
    */
    const STATUS_PENDING   = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_SHIPPED   = 'shipped';
    const STATUS_DELIVERED = 'delivered';

    /*
    |--------------------------------------------------------------------------
    | Cancelled By
    |--------------------------------------------------------------------------
    */
    const CANCELLED_BY_ADMIN    = 'admin';
    const CANCELLED_BY_VENDOR   = 'vendor';
    const CANCELLED_BY_CUSTOMER = 'customer';
    const CANCELLED_BY_SYSTEM   = 'system';


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

        'status',
        'cancel_reason_id',
        'cancel_note',
        'cancelled_by',
        'cancelled_at',

        'refund_amount',
        'refunded_at',

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

        'refund_amount'     => 'decimal:2',

        'product_snapshot'  => 'array',

        'cancelled_at'      => 'datetime',
        'refunded_at'       => 'datetime',
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

    public function cancelReason()
    {
        return $this->belongsTo(
            RedemptionCancelReason::class,
            'cancel_reason_id'
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
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusTextAttribute()
    {
        return [
            self::STATUS_PENDING   => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_SHIPPED   => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
        ][$this->status] ?? 'Unknown';
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

    public function isPending(): bool
    {
        return $this->status == self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status == self::STATUS_CONFIRMED;
    }

    public function isCancelled(): bool
    {
        return $this->status == self::STATUS_CANCELLED;
    }

    public function isShipped(): bool
    {
        return $this->status == self::STATUS_SHIPPED;
    }

    public function isDelivered(): bool
    {
        return $this->status == self::STATUS_DELIVERED;
    }


}
