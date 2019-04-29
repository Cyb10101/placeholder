<?php
namespace App\Utility;

/**
 * Class GeneralUtility
 */
class GeneralUtility {
    /**
     * @param array $color
     * @return bool
     */
    public static function isColorRGB(array $color) {
        if (!is_int($color[0]) || !is_int($color[1]) || !is_int($color[2])) {
            return false;
        }
        if ($color[0] < 0 || $color[1] < 0 || $color[2] < 0) {
            return false;
        }
        if ($color[0] > 255 || $color[1] > 255 || $color[2] > 255) {
            return false;
        }
        return true;
    }
}
