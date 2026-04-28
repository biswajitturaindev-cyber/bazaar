<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasProductType;

class ProductFashionLifestyle extends Model
{
    use HasFactory, SoftDeletes, HasProductType;

    protected $table = 'product_fashion_lifestyles';

    protected $fillable = [
        'business_id',
        'business_category_id',
        'business_sub_category_id',
        'category_id',
        'sub_category_id',
        'sub_sub_category_id',
        'hsn_id',
        'name',
        'description',
        'status',
    ];

    // ===============================
    // Business Relations
    // ===============================
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

    // ===============================
    // Category Relations
    // ===============================
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

    // ===============================
    // HSN
    // ===============================
    public function hsn()
    {
        return $this->belongsTo(Hsn::class, 'hsn_id');
    }

    // ===============================
    // Variants (Dynamic FIX)
    // ===============================
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id')
                    ->where('product_type', $this->getType());
    }
}
