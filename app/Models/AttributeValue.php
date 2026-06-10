<?php
/*
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class AttributeValue extends Model
{
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'attribute_master_id',
        'attribute_id',
        'value',
        'color_code',
        'status',
    ];

    // Value → Attribute
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
*/

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class AttributeValue extends Model
{
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'attribute_master_id',
        'value',
        'color_code',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function attributeMaster()
    {
        return $this->belongsTo(AttributeMaster::class);
    }
}
