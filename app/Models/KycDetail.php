<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycDetail extends Model
{
    protected $fillable = [
        'business_id',
        'owner_photo',
        'shop_photo',
        'pan_card',
        'gst_certificate',
        'trade_license',
        'fssai_license',
        'address_proof',
    ];

    // Relationship with Business
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
