<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterProductImage extends Model
{
    protected $fillable = [
        'master_product_id',
        'image',
        'is_primary'
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessor
    |--------------------------------------------------------------------------
    */
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationship
    |--------------------------------------------------------------------------
    */
    public function product()
    {
        return $this->belongsTo(
            MasterProduct::class,
            'master_product_id'
        );
    }
}