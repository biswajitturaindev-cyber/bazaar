<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedemptionCancelReason extends Model
{
    protected $table = 'redemption_cancel_reasons';

    public $timestamps = false;

    protected $fillable = [
        'reason',
        'status',
    ];
}
