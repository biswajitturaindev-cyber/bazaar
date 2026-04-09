<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Package extends Model
{
    protected $fillable = [
        'name',
        'stars',
        'price',
        'product_limit',
        'status'
    ];

    protected $appends = ['id'];

    public function getIdAttribute()
    {
        return Hashids::encode($this->attributes['id']);
    }

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
