<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Payment Status
    |--------------------------------------------------------------------------
    */
    const PAYMENT_PENDING  = 0;
    const PAYMENT_PAID     = 1;
    const PAYMENT_FAILED   = 2;
    const PAYMENT_REFUNDED = 3;

    /*
    |--------------------------------------------------------------------------
    | Refund Status
    |--------------------------------------------------------------------------
    */
    const REFUND_NONE    = 0;
    const REFUND_PARTIAL = 1;
    const REFUND_FULL    = 2;


    /*
    |--------------------------------------------------------------------------
    | Payment Method
    |--------------------------------------------------------------------------
    */
    const METHOD_WALLET = 0;
    const METHOD_ONLINE = 1;
    const METHOD_COD    = 2;

    /*
    |--------------------------------------------------------------------------
    | Order Status
    |--------------------------------------------------------------------------
    */
    const STATUS_PENDING    = 0;
    const STATUS_CONFIRMED  = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_SHIPPED    = 3;
    const STATUS_DELIVERED  = 4;
    const STATUS_CANCELLED  = 5;
    const STATUS_PARTIAL_CANCELLED = 6;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_no',
        'invoice_no',

        'business_id',
        'business_category_id',

        'user_id',

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

        'refund_amount',
        'refund_status',

        'payment_status',
        'payment_method',

        'order_status',
        'cancel_reason_id',
        'cancel_note',
        'cancelled_at',

        // GST
        'is_gst_bill',
        'gst_name',
        'gst_number',
        'gst_address',

        'notes',
        'placed_at',
    ];

    /**
     * Attribute Casting
     */
    protected $casts = [
        'items_total'       => 'decimal:2',
        'discount_amount'   => 'decimal:2',
        'platform_charge'   => 'decimal:2',
        'delivery_charge'   => 'decimal:2',
        'tax_amount'        => 'decimal:2',
        'grand_total'       => 'decimal:2',
        'loyalty_used'      => 'decimal:2',
        'loyalty_earned'    => 'decimal:2',
        'wallet_used'       => 'decimal:2',
        'online_paid'       => 'decimal:2',
        'refund_amount'     => 'decimal:2',
        'is_gst_bill'       => 'boolean',
        'placed_at'         => 'datetime',
        'cancelled_at'      => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function businessCategory()
    {
        return $this->belongsTo(
            BusinessCategory::class,
            'business_category_id'
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
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function cancelReason()
    {
        return $this->belongsTo(
            RedemptionCancelReason::class,
            'cancel_reason_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getPaymentStatusTextAttribute()
    {
        return [
            self::PAYMENT_PENDING  => 'Pending',
            self::PAYMENT_PAID     => 'Paid',
            self::PAYMENT_FAILED   => 'Failed',
            self::PAYMENT_REFUNDED => 'Refunded',
        ][$this->payment_status] ?? 'Unknown';
    }

    public function getRefundStatusTextAttribute()
    {
        return [
            self::REFUND_NONE    => 'No Refund',
            self::REFUND_PARTIAL => 'Partial Refund',
            self::REFUND_FULL    => 'Full Refund',
        ][$this->refund_status] ?? 'Unknown';
    }

    public function getPaymentMethodTextAttribute()
    {
        return [
            self::METHOD_WALLET => 'Wallet',
            self::METHOD_ONLINE => 'Online',
            self::METHOD_COD    => 'COD',
        ][$this->payment_method] ?? 'Unknown';
    }

    public function getOrderStatusTextAttribute()
    {
        return [
            self::STATUS_PENDING            => 'Pending',
            self::STATUS_CONFIRMED          => 'Confirmed',
            self::STATUS_PROCESSING         => 'Processing',
            self::STATUS_SHIPPED            => 'Shipped',
            self::STATUS_DELIVERED          => 'Delivered',
            self::STATUS_CANCELLED          => 'Cancelled',
            self::STATUS_PARTIAL_CANCELLED => 'Partial Cancelled',
        ][$this->order_status] ?? 'Unknown';
    }
}
