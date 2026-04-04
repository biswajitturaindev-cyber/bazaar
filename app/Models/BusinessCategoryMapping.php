<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class BusinessCategoryMapping extends Model
{
    protected $fillable = [
        'business_category_id',
        'business_sub_category_id',
        'category_id',
        'status',
    ];

    protected $appends = ['id'];

    public function getIdAttribute()
    {
        return Hashids::encode($this->attributes['id']);
    }


    // Business Category
    public function businessCategory()
    {
        return $this->belongsTo(BusinessCategory::class);
    }

    // Business Sub Category
    public function businessSubCategory()
    {
        return $this->belongsTo(BusinessSubCategory::class);
    }

    // Product Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
