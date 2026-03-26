<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessCategory extends Model
{
    protected $table = 'business_categories';

    protected $fillable = [
        'name',
        'image',
        'status',
    ];

    public function subCategories()
    {
        return $this->hasMany(BusinessSubCategory::class);
    }

}
