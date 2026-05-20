<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStatusHistory extends Model
{
    use HasFactory;

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

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'status',

        'tracking_id',

        'delivery_partner_id',
        'delivery_partner_name',

        'remarks',
        'changed_by',
    ];

    /**
     * Attribute Casting
     */
    protected $casts = [
        'status' => 'integer',
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

    public function changedBy()
    {
        return $this->belongsTo(
            User::class,
            'changed_by'
        );
    }

    public function deliveryPartner()
    {
        return $this->belongsTo(
            DeliveryPartner::class,
            'delivery_partner_id'
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
            self::STATUS_PENDING    => 'Pending',
            self::STATUS_CONFIRMED  => 'Confirmed',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED    => 'Shipped',
            self::STATUS_DELIVERED  => 'Delivered',
            self::STATUS_CANCELLED  => 'Cancelled',
        ][$this->status] ?? 'Unknown';
    }
}
