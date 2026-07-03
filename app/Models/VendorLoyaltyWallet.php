<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorLoyaltyWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'order_id',
        'order_type',
        'transaction_no',
        'transaction_type',
        'source',
        'points',
        'opening_points',
        'closing_points',
        'remarks',
        'status',
        'created_by',
    ];

    protected $casts = [
        'points' => 'decimal:2',
        'opening_points' => 'decimal:2',
        'closing_points' => 'decimal:2',
    ];


    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getAvailablePointsAttribute()
    {
        return $this->closing_points;
    }

    public function memberOrder()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
