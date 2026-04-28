<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasProductType
{
    /**
     * Get product_type dynamically from config
     */
    public function getType(): ?int
    {
        /** @var Model $this */

        static $map = null;

        if ($map === null) {
            $tableMap = config('product.table_map', []);
            $map = array_flip($tableMap);
        }

        return $map[$this->getTable()] ?? null;
    }
}
