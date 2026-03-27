<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSubCategory extends Model
{
    protected $table = 'business_sub_categories';

    protected $fillable = [
        'business_category_id',
        'name',
        'image',
        'commission',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }
}
