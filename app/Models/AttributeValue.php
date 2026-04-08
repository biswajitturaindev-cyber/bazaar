<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class AttributeValue extends Model
{
    protected $fillable = [
        'attribute_id',
        'value',
        'color_code',
        'status'
    ];

    // Value → Attribute
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
