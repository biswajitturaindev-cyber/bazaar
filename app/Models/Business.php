<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = [
        'user_id',
        'sponsor_id',
        'business_name',
        'business_category_id',
        'business_sub_category_id',
        'years_in_business',
        'gst_number',
        'pan_number',
        'fssai_license',
        'registration_number',
    ];

    // Owner
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Sponsor (User)
    // public function sponsor()
    // {
    //     return $this->belongsTo(User::class, 'sponsor_id');
    // }

    // Category
    public function category()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    // Sub Category
    public function subCategory()
    {
        return $this->belongsTo(BusinessSubCategory::class, 'business_sub_category_id');
    }

    // Address
    public function address()
    {
        return $this->hasOne(BusinessAddress::class);
    }

    // Contact
    public function contact()
    {
        return $this->hasOne(BusinessContact::class);
    }

    public function agreement()
    {
        return $this->hasOne(BusinessAgreement::class);
    }
}
