<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'stars',
        'price',
        'product_limit',
        'status'
    ];

    /**
     * One package → many vendor packages
     */
    public function vendorPackages()
    {
        return $this->hasMany(VendorPackage::class);
    }

    /**
     * Optional: get users through vendor_packages
     */
    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            VendorPackage::class,
            'package_id', // FK on vendor_packages
            'id',         // FK on users
            'id',         // local key on packages
            'user_id'     // local key on vendor_packages
        );
    }


}
