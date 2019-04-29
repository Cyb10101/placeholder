<?php
namespace App\Utility;

/**
 * Class ConvertUtility
 */
abstract class ConvertUtility {
    /**
     * Convert rgb (r, g, b) to hex (#112233)
     * @param array $rgbColor
     * @return string
     */
    public static function colorRgbToHex(array $rgbColor): string {
        return sprintf('#%02x%02x%02x', $rgbColor[0], $rgbColor[1], $rgbColor[2]);
    }

    /**
     * Convert hex (#112233 or #123) to rgb (r, g, b)
     * @param string $hexColor
     * @return array
     */
    public static function colorHexToRgb(string $hexColor): array {
        $hexColor = str_replace('#', '', $hexColor);
        if (strlen($hexColor) === 3) {
            $r = hexdec($hexColor[0].$hexColor[0]);
            $g = hexdec($hexColor[1].$hexColor[1]);
            $b = hexdec($hexColor[2].$hexColor[2]);
        } else {
            $r = hexdec($hexColor[0].$hexColor[1]);
            $g = hexdec($hexColor[2].$hexColor[3]);
            $b = hexdec($hexColor[4].$hexColor[5]);
        }
        return [$r, $g, $b];
    }

    /**
     * Steps should be between -255 and 255. Negative = darker, positive = lighter
     * @param string $hexColor
     * @param int $steps
     * @return string
     */
    public static function colorHexAdjustBrightness($hexColor, $steps) {
        $steps = max(-255, min(255, $steps));

        // Normalize into a six character long hex string
        $hexColor = str_replace('#', '', $hexColor);
        if (strlen($hexColor) === 3) {
            $hexColor = str_repeat(substr($hexColor, 0, 1),  2).str_repeat(substr($hexColor, 1, 1),  2).str_repeat(substr($hexColor, 2, 1), 2);
        }

        // Split into three parts: R, G and B
        $colorParts = str_split($hexColor, 2);
        $return = '#';

        foreach ($colorParts as $color) {
            $color   = hexdec($color); // Convert to decimal
            $color   = max(0, min(255, $color + $steps)); // Adjust color
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
        }

        return $return;
    }
}
