<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductFashionLifestyle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_fashion_lifestyles';

    protected $fillable = [
        'business_id',
        'business_category_id',
        'business_sub_category_id',
        'category_id',
        'sub_category_id',
        'sub_sub_category_id',
        'sku',
        'hsn_id',
        'name',
        'image',
        'description',
        'mrp',
        'cost_price',
        'selling_price',
        'discount',
        'final_price',
        'manufacture_date',
        'expiry_date',
        'status',
    ];

    public function productAttributes()
    {
        return $this->hasMany(ProductReviewAttribute::class, 'product_review_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function businessCategory()
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    public function businessSubCategory()
    {
        return $this->belongsTo(BusinessSubCategory::class, 'business_sub_category_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function subSubCategory()
    {
        return $this->belongsTo(SubCategoryItem::class, 'sub_sub_category_id');
    }

    public function hsn()
    {
        return $this->belongsTo(Hsn::class, 'hsn_id');
    }

    public function attributes()
    {
        return $this->morphMany(
            ProductBusinessCategoryAttributeValue::class,
            'product'
        );
    }
    public function carts()
    {
        return $this->morphMany(Cart::class, 'product');
    }
}
