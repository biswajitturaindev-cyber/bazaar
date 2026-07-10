<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class BusinessContact extends Model
{
    protected $fillable = [
        'business_id',
        'contact_person_name',
        'contact_number',
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
