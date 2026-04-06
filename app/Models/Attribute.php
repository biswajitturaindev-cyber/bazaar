<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Attribute extends Model
{
    protected $fillable = ['name', 'status'];

    // protected $appends = ['id'];

    // public function getIdAttribute()
    // {
    //     return Hashids::encode($this->attributes['id']);
    // }

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
