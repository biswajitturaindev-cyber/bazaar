<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vinkla\Hashids\Facades\Hashids;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'sub_category_id',
        'sub_sub_category_id',
        'name',
        'description',
        'hsn_id',
        'gst_percent',
        'mrp',
        'selling_price',
        'discount',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'mrp' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'gst_percent' => 'decimal:2',
    ];

    // protected $appends = ['id'];

    // public function getIdAttribute()
    // {
    //     return Hashids::encode($this->attributes['id']);
    // }

    // Main Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Sub Category
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    // Sub Sub Category
    public function subSubCategory()
    {
        return $this->belongsTo(SubCategoryItem::class, 'sub_sub_category_id');
    }

    // HSN Relation
    public function hsn()
    {
        return $this->belongsTo(Hsn::class);
    }

    // Attributes Relation
    public function attributes()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }


    // Product Images
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Optional Accessor (Final Price)
    public function getFinalPriceAttribute()
    {
        return $this->selling_price ?? $this->mrp;
    }

    // Scope Active Products
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function primaryVariant()
    {
        return $this->hasOne(ProductVariant::class, 'product_id')
            ->where('is_primary', 1);
    }

}
