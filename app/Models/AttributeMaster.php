<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeMaster extends Model
{
    protected $fillable = [
        'business_category_id',
        'business_sub_category_id',
        'name'
    ];

    // Master → Attributes
    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }
}
