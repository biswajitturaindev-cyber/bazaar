<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class BusinessCategory extends Model
{
    protected $table = 'business_categories';

    protected $fillable = [
        'name',
        'image',
        'status',
    ];

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'business_category_mappings',
            'business_category_id',
            'category_id'
        );
    }

    public function subCategories()
    {
        return $this->hasMany(BusinessSubCategory::class);
    }

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

    public function mappings()
    {
        return $this->hasMany(BusinessCategoryMapping::class);
    }

}
