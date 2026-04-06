<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class AttributeValue extends Model
{
    protected $fillable = ['attribute_id', 'value', 'color_code', 'status'];

    // protected $appends = ['id'];

    // public function getIdAttribute()
    // {
    //     return Hashids::encode($this->attributes['id']);
    // }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
