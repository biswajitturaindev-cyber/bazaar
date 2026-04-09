<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class StoreOperationalDetail extends Model
{
    protected $table = 'store_operational_details';

    protected $fillable = [
        'business_id',
        'opening_time',
        'closing_time',
        'delivery_type',
        'delivery_radius',
        'serviceable_pincode',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];


    protected $appends = ['id'];

    public function getIdAttribute()
    {
        return Hashids::encode($this->attributes['id']);
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    // Convert string → array
    public function getServiceablePincodeAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    // Convert array → string
    public function setServiceablePincodeAttribute($value)
    {
        $this->attributes['serviceable_pincode'] = is_array($value)
            ? implode(',', $value)
            : $value;
    }

    // Check store open or not
    public function isOpenNow()
    {
        $now = now()->format('H:i:s');

        // Overnight case (10 PM → 2 AM)
        if ($this->closing_time < $this->opening_time) {
            return ($now >= $this->opening_time || $now <= $this->closing_time);
        }

        return ($now >= $this->opening_time && $now <= $this->closing_time);
    }
}
