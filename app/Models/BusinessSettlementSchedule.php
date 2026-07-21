<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSettlementSchedule extends Model
{
    protected $fillable = [
        'business_id',
        'type',
        'day',
    ];

    const TYPE_DAILY = 'daily';
    const TYPE_WEEKLY = 'weekly';
    const TYPE_MONTHLY = 'monthly';

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
