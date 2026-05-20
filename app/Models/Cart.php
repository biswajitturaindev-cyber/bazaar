<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'business_category_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'product_name',
        'attribute_hash',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // public function user()
    // {
    //     return $this->belongsTo(
    //         User::class,
    //         'user_id'
    //     );
    // }

    /*
    |--------------------------------------------------------------------------
    | Relationships
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

    public function product()
    {
        $modelClass = $this->getProductModelClass();

        if (!$modelClass) {
            return null;
        }

        return $modelClass::find($this->product_id);
    }

}
