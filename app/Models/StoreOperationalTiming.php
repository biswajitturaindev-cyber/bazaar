<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class StoreOperationalTiming extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_operational_detail_id',
        'opening_time',
        'closing_time',
    ];

    /**
     * Get the operational detail that owns this timing.
     */
    public function storeOperationalDetail()
    {
        return $this->belongsTo(StoreOperationalDetail::class);
    }
}
