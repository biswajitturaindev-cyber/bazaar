<?php

use Intervention\Image\Interfaces\ImageInterface;

if (!function_exists('compressToTargetSize')) {

    function compressToTargetSize(ImageInterface $image, $targetKB = 30)
    {
        $quality = 90;

        do {
            $encoded = (string) $image->toWebp($quality);
            $sizeKB = strlen($encoded) / 1024;
            $quality -= 5;

        } while ($sizeKB > $targetKB && $quality > 10);

        return $encoded;
    }
}
