<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSettlementTransaction extends Model
{
    protected $fillable = [
        'business_id',
        'transaction_no',

        // Commission
        'payable_commission',
        'settlement_amount',

        // Payment
        'payment_mode',
        'payment_transaction_no',
        'payment_reference_no',
        'payment_slip',

        // Vendor remarks
        'remarks',

        // Status
        'status',

        // Admin
        'approved_by',
        'approved_at',
        'admin_remarks',

        // Final settlement
        'settlement_reference_no',
        'paid_at',
    ];

    protected $casts = [
        'payable_commission' => 'decimal:2',
        'settlement_amount'  => 'decimal:2',
        'approved_at'        => 'datetime',
        'paid_at'            => 'datetime',
    ];

    /**
     * Business
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Admin who approved the request
     */
    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function settlementOrders()
    {
        return $this->hasMany(
            CommissionSettlementOrder::class,
            'settlement_transaction_id'
        );
    }
}
