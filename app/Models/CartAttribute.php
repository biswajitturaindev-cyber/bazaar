<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartAttribute extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'cart_id',
        'attribute_master_id',
        'attribute_value_id',
        'attribute_master_name',
        'attribute_value',
        'price'
    ];

    /**
     * Attribute Casting
     */
    protected $casts = [
        'cart_id' => 'integer',
        'attribute_master_id' => 'integer',
        'attribute_value_id' => 'integer',
        'price' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function cart()
    {
        return $this->belongsTo(
            Cart::class,
            'cart_id'
        );
    }

    public function attributeMaster()
    {
        return $this->belongsTo(
            AttributeMaster::class,
            'attribute_master_id'
        );
    }

    public function attributeValue()
    {
        return $this->belongsTo(
            AttributeValue::class,
            'attribute_value_id'
        );
    }
}
