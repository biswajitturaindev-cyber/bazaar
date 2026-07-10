<?php

namespace App\Traits;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasPrimaryVariant
{
    public function primaryVariant()
    {
        $type = $this->resolveType();

        return $this->hasOne(\App\Models\ProductVariant::class, 'product_id', 'id')
            ->where('is_primary', 1)
            ->where('product_type', $type);
    }

    protected function resolveType()
    {
        static $map = null;

        if ($map === null) {
            $map = config('product.table_map');
        }

        $table = (new static)->getTable();

        $type = array_search($table, $map, true);

        if ($type === false) {
            throw new \Exception("Product type not found for table: {$table}");
        }

        return $type;
    }
}
