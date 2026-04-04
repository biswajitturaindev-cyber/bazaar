<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MasterProducts;
use Vinkla\Hashids\Facades\Hashids;

class SubCategory extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subSubCategories()
    {
        return $this->hasMany(SubCategoryItem::class, 'sub_category_id');
    }


    public function products()
    {
        return $this->hasMany(Product::class);
    }



}
