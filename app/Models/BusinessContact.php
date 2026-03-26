<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessContact extends Model
{
    protected $fillable = [
        'business_id',
        'contact_person_name',
        'contact_number',
        'agree_terms',
        'confirm_info',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
