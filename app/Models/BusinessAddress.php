<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
