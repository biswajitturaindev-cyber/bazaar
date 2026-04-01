<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    protected $fillable = [
        'business_id',
        'account_holder_name',
        'bank_name',
        'account_number',
        'ifsc_code',
        'upi_id',
        'cancelled_cheque',
    ];

    // Relationship with Business
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
