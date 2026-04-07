<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class KycDetail extends Model
{
    protected $fillable = [
        'business_id',
        'owner_photo',
        'owner_photo_status',
        'shop_photo',
        'shop_photo_status',
        'pan_card',
        'pan_card_status',
        'gst_certificate',
        'gst_certificate_status',
        'trade_license',
        'trade_license_status',
        'fssai_license',
        'fssai_license_status',
        'address_proof',
        'address_proof_status'
    ];

    protected $appends = ['id'];

    public function getIdAttribute()
    {
        return Hashids::encode($this->attributes['id']);
    }

    // Relationship with Business
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }
}
