<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;
class StoreOperationalDetail extends Model
{
    protected $table = 'store_operational_details';

    protected $fillable = [
        'business_id',
        //'opening_time',
        //'closing_time',
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


    public function timings()
    {
        return $this->hasMany(StoreOperationalTiming::class);
    }

    // Check store open or not
    public function isOpenNow(): bool
    {
        $now = Carbon::now()->format('H:i:s');

        $timings = $this->relationLoaded('timings')
            ? $this->timings
            : $this->timings()->get();

        return $timings->contains(function ($timing) use ($now) {
            return $timing->status
                && $now >= $timing->opening_time
                && $now <= $timing->closing_time;
        });
    }
}
