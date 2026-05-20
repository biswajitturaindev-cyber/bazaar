<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryPartner extends Model
{
    use HasFactory;

    const INACTIVE = 0;
    const ACTIVE   = 1;

    protected $fillable = [
        'name',
        'code',
        'website',
        'tracking_url',
        'contact_no',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function orderStatusHistories()
    {
        return $this->hasMany(
            OrderStatusHistory::class,
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
        return $this->status == self::ACTIVE
            ? 'Active'
            : 'Inactive';
    }
}
