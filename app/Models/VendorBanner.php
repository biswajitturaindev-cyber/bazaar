<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorBanner extends Model
{
    protected $fillable = [
        'business_id',
        'banner_type',
        'title',
        'image',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
