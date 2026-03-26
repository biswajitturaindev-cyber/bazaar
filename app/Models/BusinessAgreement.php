<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessAgreement extends Model
{
    protected $fillable = [
        'business_id',
        'agree_terms',
        'confirm_info',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
