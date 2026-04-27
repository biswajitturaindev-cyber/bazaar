<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeMaster extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_category_id',
        'business_sub_category_id',
        'name'
    ];

    protected $casts = [
        'business_category_id' => 'integer',
        'business_sub_category_id' => 'integer',
    ];

    /**
     * Attribute Master → Attributes
     */
    public function attributes()
    {
        return $this->hasMany(Attribute::class, 'attribute_master_id');
    }

    /**
     * Attribute Master → Business Category
     */
    public function category()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    /**
     * Attribute Master → Business Sub Category
     */
    public function subCategory()
    {
        return $this->belongsTo(BusinessSubCategory::class, 'business_sub_category_id');
    }
}
