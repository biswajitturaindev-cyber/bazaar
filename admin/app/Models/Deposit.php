<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'company_account_id',
        'amount',
        'transaction_id',
        'ref_id',
        'payment_method',
        'payment_proof',
        'status',
        'admin_note',
        'user_note',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Payment Methods
    const PAYMENT_UPI = 1;
    const PAYMENT_BANK = 2;
    const PAYMENT_WALLET = 3;

    // Status
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REJECTED = 2;

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    // public function companyAccount()
    // {
    //     return $this->belongsTo(CompanyAccount::class);
    // }

    public function getPaymentMethodLabelAttribute()
    {
        return match ($this->payment_method) {
            self::PAYMENT_UPI => 'UPI',
            self::PAYMENT_BANK => 'Bank',
            self::PAYMENT_WALLET => 'Wallet / Other',
            default => 'N/A',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown',
        };
    }
}
