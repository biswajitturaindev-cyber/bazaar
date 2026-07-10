<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterProducts extends Model
{
    protected $table = 'master_products';

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'sub_sub_category_id',
        'hsn_id',

        'name',
        'image',

        'batch_no',
        'manufacturing_date',
        'expiry_date',

        'product_price',
        'selling_price',

        'description',
        'commission',

        'status',
    ];

    protected $casts = [
        'manufacturing_date' => 'date',
        'expiry_date' => 'date',

        'product_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'commission' => 'decimal:2',

        'status' => 'boolean',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class,'sub_category_id');
    }

    public function subSubCategory()
    {
        return $this->belongsTo(
            SubCategoryItem::class,
            'sub_sub_category_id'
        );
    }

    public function hsn()
    {
        return $this->belongsTo(Hsn::class, 'hsn_id');
    }

    // public function combos()
    // {
    //     return $this->belongsToMany(
    //         combos::class,
    //         'combo_products',
    //         'product_id',
    //         'combo_id'
    //     );
    // }
}
