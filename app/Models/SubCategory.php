<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MasterProducts;
class SubCategory extends Model
{
    protected $fillable = [
        'category_id',
        // 'parent_id',
        'name',
        'description',
        'status'
    ];

    // public function parent()
    // {
    //     return $this->belongsTo(SubCategory::class,'parent_id');
    // }

    // public function children()
    // {
    //     return $this->hasMany(SubCategory::class,'parent_id');
    // }

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
