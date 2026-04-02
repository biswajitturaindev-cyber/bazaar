<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPackage extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'package_id',
        'start_date',
        'end_date'
    ];

    /**
     * Vendor (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Package
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Check if package is active
     */
    public function isActive()
    {
        return is_null($this->end_date) || $this->end_date >= now();
    }
}
