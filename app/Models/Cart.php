<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_category_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'product_name',
        'attribute_hash',
    ];

    protected $casts = [
        'user_id'              => 'integer',
        'business_category_id' => 'integer',
        'product_id'           => 'integer',
        'product_variant_id'   => 'integer',
        'quantity'             => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Cart Attributes
    |--------------------------------------------------------------------------
    */

    public function cartAttributes()
    {
        return $this->hasMany(
            CartAttribute::class,
            'cart_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Product Variant
    |--------------------------------------------------------------------------
    */

    public function productVariant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Dynamic Product Resolver
    |--------------------------------------------------------------------------
    */

    public function getProductModelClass()
    {
        return config('product.model_map')[
            $this->business_category_id
        ] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | Dynamic Product
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        $modelClass = $this->getProductModelClass();

        if (!$modelClass) {
            return null;
        }

        return $modelClass::find(
            $this->product_id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getFinalPriceAttribute()
    {
        return $this->productVariant?->final_price ?? 0;
    }

    public function getSubtotalAttribute()
    {
        return (
            $this->final_price
            * $this->quantity
        );
    }

    public function getTotalAttribute()
    {
        return $this->subtotal;
    }

    /*
    |--------------------------------------------------------------------------
    | Variant Attributes Helper
    |--------------------------------------------------------------------------
    */

    public function getVariantAttributesAttribute()
    {
        return $this->productVariant?->attributes ?? [];
    }


}
