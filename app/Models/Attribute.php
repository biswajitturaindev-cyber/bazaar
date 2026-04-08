<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Attribute extends Model
{
    protected $fillable = [
        'attribute_master_id',
        'type',
        'name',
        'status'
    ];

    // Attribute → Master
    public function master()
    {
        return $this->belongsTo(AttributeMaster::class, 'attribute_master_id');
    }

    // Attribute → Values
    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
