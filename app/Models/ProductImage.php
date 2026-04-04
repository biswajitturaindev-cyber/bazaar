<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_large',
        'image_medium',
        'image_small'
    ];

    protected $appends = ['id'];

    public function getIdAttribute()
    {
        return Hashids::encode($this->attributes['id']);
    }

    // 🔗 Relationship
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
