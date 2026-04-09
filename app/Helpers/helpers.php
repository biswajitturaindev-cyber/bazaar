<?php

use Hashids\Hashids;
use Illuminate\Support\Facades\DB;

if (!function_exists('decodeIdOrFail')) {

    function decodeIdOrFail($id, $message = 'Invalid ID')
    {
        $decoded = app(Hashids::class)->decode($id);

        if (empty($decoded)) {
            abort(response()->json([
                'status'  => false,
                'message' => $message
            ], 400));
        }

        return $decoded[0];
    }
}


function getProductByImage($image)
{
    $tableMap = [
        1 => 'product_food_beverages',
        2 => 'product_construction_hardware',
        3 => 'product_home_livings',
        4 => 'product_fashion_lifestyles',
        5 => 'product_automobiles',
        7 => 'product_agricultures',
        8 => 'product_retail_general',
        9 => 'product_healths',
    ];

    $table = $tableMap[$image->business_category_id] ?? null;

    if (!$table) return null;

    return DB::table($table)
        ->where('id', $image->product_id)
        ->first();
}
