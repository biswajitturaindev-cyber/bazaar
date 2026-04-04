<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class BusinessAgreement extends Model
{
    protected $fillable = [
        'business_id',
        'agree_terms',
        'confirm_info',
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
