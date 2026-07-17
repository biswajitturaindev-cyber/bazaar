<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSettlementOrder extends Model
{
    const PLATFORM_CHARGE = 2.00;
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_id',
        'business_id',
        'settlement_transaction_id',
        'order_amount',
        'platform_charge',
        'settlement_order_amount',
        'commission_amount',
        'status',
        'settled_at',
    ];

    protected $casts = [
        'order_amount' => 'decimal:2',
        'platform_charge' => 'decimal:2',
        'settlement_order_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'settled_at' => 'datetime',
    ];

    /**
     * Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Business
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Settlement Transaction
     */
    public function settlementTransaction()
    {
        return $this->belongsTo(
            CommissionSettlementTransaction::class,
            'commission_settlement_transaction_id'
        );
    }
}
