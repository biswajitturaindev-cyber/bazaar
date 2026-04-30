<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'product_type',
        'sku',
        'barcode',
        'mrp',
        'cost_price',
        'selling_price',
        'discount',
        'final_price',
        'is_primary',
        'manufacture_date',
        'expiry_date',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'mrp' => 'float',
        'cost_price' => 'float',
        'selling_price' => 'float',
        'discount' => 'float',
        'final_price' => 'float',
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Optimized Dynamic Product Accessor (with caching)
     */
    public function getProductAttribute()
    {
        static $cache = [];

        $key = $this->product_type . '_' . $this->product_id;

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $modelMap = config('product.model_map');

        if (!isset($modelMap[$this->product_type])) {
            return null;
        }

        $modelClass = $modelMap[$this->product_type];

        return $cache[$key] = $modelClass::find($this->product_id);
    }

    /**
     * Optional direct method
     */
    public function getProductModel()
    {
        return $this->product;
    }

    /**
     * Scope: Only Primary
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', 1);
    }

    /**
     * Meta (1:1)
     */
    public function meta()
    {
        return $this->hasOne(ProductVariantMeta::class, 'product_variant_id');
    }

    /**
     * Attributes
     */
    public function attributes()
    {
        return $this->hasMany(ProductAttributeRelation::class, 'product_variant_id');
    }

    /**
     * Vendor Stocks
     */
    public function stocks()
    {
        return $this->hasMany(ProductVendorStock::class, 'product_variant_id');
    }

    /**
     * Images
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_variant_id');
    }
}
