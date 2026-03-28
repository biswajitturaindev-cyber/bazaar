<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'status',
        'image'
    ];

    public function mappings()
    {
        return $this->hasMany(BusinessCategoryMapping::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
