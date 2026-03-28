<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hsn extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'hsn_code',
        'description',
        'cgst',
        'sgst',
        'igst',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'cgst' => 'decimal:2',
        'sgst' => 'decimal:2',
        'igst' => 'decimal:2',
    ];
}
