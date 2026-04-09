<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class BusinessAddress extends Model
{
    protected $fillable = [
        'business_id',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'pincode',
        'landmark',
        'google_map_location',
        'latitude',
        'longitude'
    ];


    protected $appends = ['id'];

    public function getIdAttribute()
    {
        return Hashids::encode($this->attributes['id']);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
