<?php

namespace App\Models;
use App\Traits\HasPrimaryVariant;
use App\Traits\HasProductType;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductHealth extends Model
{
    use HasFactory, SoftDeletes, HasProductType, HasPrimaryVariant;

    protected $table = 'product_healths';

    protected $fillable = [
        'business_id',
        'business_category_id',
        'business_sub_category_id',
        'category_id',
        'sub_category_id',
        'sub_sub_category_id',
        'hsn_id',
        'has_variant',
        'name',
        'commission',
        'vendor_commission',
        'vendor_commission_approval_status',
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
        $type = array_search(static::class, config('product.model_map'));

        return $this->hasMany(ProductVariant::class, 'product_id')
            ->where('product_type', $type);
    }

    // ===============================
    // PrimaryVariant
    // ===============================
    public function primaryVariant()
    {
        $type = array_search(static::class, config('product.model_map'));

        return $this->hasOne(ProductVariant::class, 'product_id')
            ->where('product_type', $type)
            ->where('is_primary', 1);
    }

    public function carts()
    {
        return $this->morphMany(Cart::class, 'product');
    }

    public function getVendorCommissionApprovalStatusTextAttribute()
    {
        return [
            0 => 'Waiting for Approval',
            1 => 'Approved',
            2 => 'Rejected',
        ][$this->vendor_commission_approval_status] ?? 'Unknown';
    }
}
