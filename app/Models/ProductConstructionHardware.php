<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductConstructionHardware extends Model
{
    use HasFactory;

    protected $table = 'product_construction_hardware';

    protected $fillable = [
        'business_id',
        'business_sub_category_id',
        'category_id',
        'sub_category_id',
        'sku',
        'slug',
        'name',
        'description',
        'mrp',
        'cost_price',
        'selling_price',
        'discount_percent',
        'discount_amount',
        'stock',
        'manufacture_date',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'mrp' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'stock' => 'integer',
        'status' => 'integer',
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function businessSubCategory()
    {
        return $this->belongsTo(BusinessSubCategory::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
