<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_large',
        'image_medium',
        'image_small'
    ];

    // 🔗 Relationship
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
