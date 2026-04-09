<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterProducts extends Model
{
    protected $table = 'master_products';

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'name',
        'sku',
        'description',
        'price',
        'prod_pv',
        'prod_bv',
        'stock',
        'image',
        'status'
    ];
    
    
    
    public function category()
{
    return $this->belongsTo(Category::class,'category_id');
}

public function subcategory()
{
    return $this->belongsTo(SubCategory::class,'sub_category_id');
}


public function combos()
    {
        return $this->belongsToMany(
            Combo::class,
            'combo_products',
            'product_id',
            'combo_id'
        );
    }
}