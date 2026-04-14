<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'description',
        'status',
        'image'
    ];

    // protected $appends = ['id'];

    // public function getIdAttribute()
    // {
    //     return Hashids::encode($this->attributes['id']);
    // }


    public function mappings()
    {
        return $this->hasMany(BusinessCategoryMapping::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }
}
