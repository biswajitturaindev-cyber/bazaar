<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['name','state_code','gst_code'];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
