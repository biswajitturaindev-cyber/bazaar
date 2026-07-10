<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vinkla\Hashids\Facades\Hashids;

class Attribute extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'attribute_master_id',
        'type',
        'name',
        'status'
    ];

    // Attribute → Master
    public function attributeMaster()
    {
        return $this->belongsTo(AttributeMaster::class, 'attribute_master_id');
    }

    // Attribute → Values
    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    // Attribute → Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Attribute → Sub Category
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

}
