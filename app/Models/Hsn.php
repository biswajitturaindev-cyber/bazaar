<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hsn extends Model
{
    protected $table = 'hsn';

    protected $fillable = [
        'hsnCode',
        'description',
        'cGst',
        'sGst',
        'iGst',
        'isActive'
    ];

    public function getHsnDisplayAttribute()
    {
        return $this->hsnCode . ' - ' . $this->description;
    }
}