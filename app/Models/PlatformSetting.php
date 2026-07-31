<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = [
        'platform_fee',
        'settlement_fee',
        'status',
    ];

    protected $casts = [
        'platform_fee' => 'decimal:2',
        'settlement_fee' => 'decimal:2',
        'status' => 'boolean',
    ];
}
