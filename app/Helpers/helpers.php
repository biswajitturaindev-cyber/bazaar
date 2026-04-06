<?php

use Hashids\Hashids;

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
