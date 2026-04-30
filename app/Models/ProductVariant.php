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
        'manufacture_date',
        'expiry_date'
    ];

    /**
     * Dynamic Product Accessor
     * Usage: $variant->product
     */
    public function getProductAttribute()
    {
        $modelMap = config('product.model_map');

        if (!isset($modelMap[$this->product_type])) {
            return null;
        }

        $modelClass = $modelMap[$this->product_type];

        return $modelClass::find($this->product_id);
    }

    /**
     * Optional direct method
     */
    public function getProductModel()
    {
        return $this->getProductAttribute();
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
