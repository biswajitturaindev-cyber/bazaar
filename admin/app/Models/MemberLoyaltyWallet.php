<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MemberLoyaltyWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'order_id',
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

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }


}
