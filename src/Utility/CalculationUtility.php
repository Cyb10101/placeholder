<?php
namespace App\Utility;

/**
 * Class CalculationUtility
 */
abstract class CalculationUtility {
    /**
     * @param $originalWidth
     * @param $originalHeight
     * @param $width
     * @param $height
     * @param int $saveOriginal
     * @return \stdClass
     * @todo In development
     */
    public static function calcShrinkBox($originalWidth, $originalHeight, $width, $height, $saveOriginal = 0) {
        if ($saveOriginal === 1 AND (($height > $originalHeight) OR ($width > $originalWidth))) {
            $width = $originalWidth;
            $height = $originalHeight;
        } elseif (($height / $originalHeight) > ($width / $originalWidth)) {
            $height = round($originalHeight * ($width / $originalWidth));
        } else {
            $width = round($originalWidth * ($height / $originalHeight));
        }

        $return = new \stdClass();
        $return->width = $width;
        $return->height = $height;
        return $return;
    }

    /**
     * @param $originalWidth
     * @param $originalHeight
     * @param $width
     * @param $height
     * @return \stdClass
     * @todo In development
     */
    public static function calcShrinkBoxCover($originalWidth, $originalHeight, $width, $height) {
        $newWidth = $height / $originalHeight * $originalWidth;
        $newHeight = $width / $originalWidth * $originalHeight;
        if ($newWidth < $width) {
            $newWidth = $width;
        } elseif ($newHeight < $height) {
            $newHeight = $height;
        }

        $return = new \stdClass();
        $return->width = $newWidth;
        $return->height = $newHeight;
        return $return;
    }

    /**
     * @param $originalWidth
     * @param $originalHeight
     * @param $width
     * @param $height
     * @return \stdClass
     * @todo In development
     */
    public function calcGap($originalWidth, $originalHeight, $width, $height) {
        $gapWidth = 0; $gapHeight = 0;
        if ($originalWidth > $width) {
            $gapWidth = ($originalWidth - $width) / 2;
        }
        if($originalHeight > $height) {
            $gapHeight = ($originalHeight - $height) / 2;
        }

        $return = new \stdClass();
        $return->width = $gapWidth;
        $return->height = $gapHeight;
        return $return;
    }
}
