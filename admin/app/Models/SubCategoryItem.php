<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategoryItem extends Model
{
    protected $table = 'sub_category_items';

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'name',
        'description',
        'status',
        'image'
    ];


    // Relationship: Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship: Sub Category
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'sub_sub_category_id');
    }
}
