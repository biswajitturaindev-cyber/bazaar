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
        'attribute_id',
        'attribute_value_id',
        'attribute_name',
        'attribute_value',
    ];

    /**
     * Attribute Casting
     */
    protected $casts = [
        'cart_id'            => 'integer',
        'attribute_id'       => 'integer',
        'attribute_value_id' => 'integer',
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

    public function attribute()
    {
        return $this->belongsTo(
            Attribute::class,
            'attribute_id'
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
