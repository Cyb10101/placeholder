<?php
namespace App\Utility;

/**
 * Class MagickUtility
 *
 * @author Thomas Schur <cyb10101@gmail.com>
 */
class MagickUtility extends Singleton {
    /**
     * @var string
     */
    protected $binaryGmagick = '/usr/bin/gm';

    /**
     * @var string
     */
    protected $binaryConvertImagick = '/usr/bin/convert';

    /**
     * @var bool
     */
    protected $existsGraphicMagick = null;

    /**
     * @var bool
     */
    protected $existsImageMagick = null;

    /**
     * @var string
     */
    protected $lastCommand = '';

    /**
     * @return MagickUtility
     */
    public static function getInstance(): MagickUtility {
        return parent::getInstance();
    }

    /**
     * Check if one Magick exists
     *
     * @return bool
     */
    public function isMagick(): bool {
        return ($this->existsGraphicMagick() || $this->existsImageMagick());
    }

    /**
     * Checks if the GraphicsMagick CLI exists
     *
     * @return bool
     */
    public function existsGraphicMagick(): bool {
        if (is_null($this->existsGraphicMagick)) {
            $output = null;
            $return = null;
            exec('gm -version >/dev/null 2>&1', $output, $return);
            $this->existsGraphicMagick = ($return === 0);
        }
        return $this->existsGraphicMagick;
    }

    /**
     * Checks if the ImageMagick CLI exists
     *
     * @return bool
     */
    public function existsImageMagick(): bool {
        if (is_null($this->existsImageMagick)) {
            $output = null;
            $return = null;
            exec('identify -version >/dev/null 2>&1', $output, $return);
            $this->existsImageMagick = ($return === 0);
        }
        return $this->existsImageMagick;
    }

    /**
     * @return bool
     */
    public function forceGraphicMagick(): bool {
        $this->existsGraphicMagick = null;
        $this->existsImageMagick = false;
        return $this->existsGraphicMagick();
    }

    /**
     * @return bool
     */
    public function forceImageMagick(): bool {
        $this->existsGraphicMagick = false;
        $this->existsImageMagick= null;
        return $this->existsImageMagick();
    }

    /**
     * @param string $outputFile
     * @param int $width
     * @param int $height
     * @param string $backgroundColor
     * @return bool
     */
    public function createCanvas(string $outputFile, int $width, int $height, string $backgroundColor): bool {
        $result = $this->executeConvert([
            '-size ' . $width . 'x' . $height,
            'xc:' . $backgroundColor . '',
            $outputFile
        ]);
        return $result;
    }


    /**
     * @param string $inputFile
     * @param string $outputFile
     * @param int $width
     * @param int $height
     * @param string $text
     * @param string $font
     * @param int $fontSize
     * @param string $textColor
     * @param string $shadowColor
     * @return bool
     */
    public function textCenter(string $inputFile, string $outputFile, int $width, int $height, $text, string $font, int $fontSize = 100, string $textColor = '#ffffff', $shadowColor = '#7f7f7f'): bool {
        $fontSize = CalculationUtility::gdReduceFontSize($text, $font, $fontSize, $width);

        $result = $this->executeConvert([
            '-gravity center',
            '-font ' . $font,
            '-pointsize ' . $fontSize,
            '-fill "' . $shadowColor . '"',
            '-draw "text 1,1 \'' . $text . '\'"',
            '-fill "' . $textColor . '"',
            '-draw "text 0,0 \'' . $text . '\'"',
            $inputFile,
            $outputFile
        ]);

        return $result;
    }

    /**
     * @param string $inputFile
     * @param string $outputFile
     * @param int $width
     * @param int $height
     * @param $text
     * @param string $font
     * @param int $fontSize
     * @param string $textColor
     * @param string $shadowColor
     * @return bool
     */
    public function textBottomLeft(string $inputFile, string $outputFile, int $width, int $height, $text, string $font, int $fontSize = 100, int $border = 0, string $textColor = '#ffffff', $shadowColor = '#7f7f7f'): bool {
        $fontSize = CalculationUtility::gdReduceFontSize($text, $font, $fontSize, $height);
        $fontSize += 4;

        if ($this->existsGraphicMagick) {
            $yText = $fontSize + $border;
        } else {
            $yText = $border;
        }

        $result = $this->executeConvert([
            $inputFile,
            '-pointsize ' . $fontSize,
            '-font ' . $font,

            '-rotate 90',
            '-gravity NorthWest',

            '-fill "' . $shadowColor . '"',
            '-draw "text ' . ($border + 5) . ',' . ($yText + 1) . ' \'' . $text . '\'"',

            '-fill "' . $textColor . '"',
            '-draw "text ' . ($border + 4) . ',' . $yText . ' \'' . $text . '\'"',

            '-rotate -90',
            $outputFile
        ]);
        return $result;
    }

    /**
     * @param string $inputFile
     * @param string $outputFile
     * @param int $thickness
     * @param string $borderColor
     * @return bool
     */
    public function drawBorder(string $inputFile, string $outputFile, $thickness = 1, $borderColor = '#ffffff'): bool {
        $result = $this->executeConvert([
            '-shave ' . $thickness . 'x' . $thickness,
            '-border ' . $thickness . 'x' . $thickness,
            '-bordercolor "' . $borderColor . '"',
            $inputFile,
            $outputFile
        ]);
        return $result;
    }

    /**
     * @param string $inputFile
     * @param string $outputFile
     * @param int $width
     * @param int $height Zero to aspect ratio
     * @return bool
     */
    public function thumbnailUncut(string $inputFile, string $outputFile, int $width, int $height = 0): bool {
        $result = $this->executeConvert([
            '-background transparent',
            $inputFile,
            '-coalesce',
            '-thumbnail ' . $width . ($height > 0 ? 'x' . $height : ''),
            $outputFile
        ]);
        return $result;
    }

    /**
     * @param string $inputFile
     * @param string $outputFile
     * @param int $width
     * @param int $height
     * @return bool
     */
    public function thumbnailCut(string $inputFile, string $outputFile, int $width, int $height): bool {
        $result = $this->executeConvert([
            '-background transparent',
            $inputFile,
            '-coalesce',
            '-thumbnail ' . $width . 'x' . $height . '^',
            '-gravity center',
            '-extent ' . $width . 'x' . $height,
            $outputFile
        ]);
        return $result;
    }

    protected function createCommandException() {
        throw new \Exception('Command not working: ' . PHP_EOL . $this->lastCommand);
    }

    /**
     * @param array $commands
     * @return bool
     */
    protected function executeConvert(array $commands): bool {
        $output = null;
        $return = null;
        if ($this->existsGraphicMagick()) {
            $this->lastCommand = $this->binaryGmagick . ' convert ' . implode(' ', $commands);
            exec($this->lastCommand, $output, $return);
            return ($return === 0);
        } else if ($this->existsImageMagick()) {
            $this->lastCommand = $this->binaryConvertImagick . ' ' . implode(' ', $commands);
            exec($this->lastCommand, $output, $return);
            return ($return === 0);
        }
        return false;
    }
}
