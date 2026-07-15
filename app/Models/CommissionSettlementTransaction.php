<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSettlementTransaction extends Model
{
    protected $fillable = [
        'business_id',
        'transaction_no',
        'payable_commission',
        'settlement_amount',
        'payment_mode',
        'remarks',
        'status',
        'approved_by',
        'approved_at',
        'admin_remarks',
        'payment_reference',
        'paid_at',
    ];

    protected $casts = [
        'payable_commission' => 'decimal:2',
        'settlement_amount'  => 'decimal:2',
        'approved_at'        => 'datetime',
        'paid_at'            => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
