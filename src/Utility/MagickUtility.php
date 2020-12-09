<?php
namespace App\Utility;

class MagickUtility extends Singleton {
    protected string $binaryGmagick = '/usr/bin/gm';
    protected string $binaryConvertImagick = '/usr/bin/convert';
    protected bool $existsMagick = false;
    protected bool $existsGraphicMagick = false;
    protected bool $existsImageMagick = false;
    protected array $stacks = [];
    protected string $lastCommand = '';

    public static function getInstance(): MagickUtility {
        return parent::getInstance();
    }

    public function initializeInstance() {
        if (!$this->isMagick()) {
            throw new \Exception('Magick not found');
        }
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
        if (!$this->existsMagick) {
            $output = null;
            $return = null;
            exec($this->binaryGmagick . ' -version', $output, $return);
            $this->existsGraphicMagick = ($return === 0);
            if ($this->existsGraphicMagick) {
                $this->existsMagick = true;
            }
        }
        return $this->existsGraphicMagick;
    }

    /**
     * Checks if the ImageMagick CLI exists
     *
     * @return bool
     */
    public function existsImageMagick(): bool {
        if (!$this->existsMagick) {
            $output = null;
            $return = null;
            exec($this->binaryConvertImagick . ' -version >/dev/null 2>&1', $output, $return);
            $this->existsImageMagick = ($return === 0);
            if ($this->existsImageMagick) {
                $this->existsMagick = true;
            }
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
     * @return self
     */
    public function addStacks(array $stack) {
        $this->stacks[] = $stack;
        return $this;
    }

    /**
     * @return self
     */
    public function clearStacks(): self {
        $this->stacks = [];
        return $this;
    }

    /**
     * @param string $outputFile
     * @param int $width
     * @param int $height
     * @param string $backgroundColor
     * @return bool
     */
    public function createCanvas(string $outputFile, int $width, int $height, string $backgroundColor): bool {
        $result = $this->addStacks([
            '-size ' . $width . 'x' . $height,
            'xc:' . $backgroundColor . '',
//            $outputFile
        ]);
        return true;
    }

    /**
     * @param string $inputFile
     * @return MagickUtility
     */
    public function addInputFile(string $inputFile): MagickUtility {
        $this->addStacks([
            '"' . $inputFile . '"',
        ]);
        return $this;
    }

    /**
     * @param string $outputFile
     * @return MagickUtility
     */
    public function addOutputFile(string $outputFile): MagickUtility {
        $this->addStacks([
            '"' . $outputFile . '"',
        ]);
        return $this;
    }

    /**
     * @param int $quality
     * @return MagickUtility
     */
    public function quality(int $quality): MagickUtility {
        $this->addStacks([
            '-quality ' . $quality,
        ]);
        return $this;
    }

    /**
     * @param int $width
     * @param int $height
     * @param string $text
     * @param string $font
     * @param int $fontSize
     * @param string $textColor
     * @param string $shadowColor
     * @return MagickUtility
     */
    public function textCenter(int $width, int $height, $text, string $font, int $fontSize = 100, string $textColor = '#ffffff', $shadowColor = '#7f7f7f'): MagickUtility {
        $fontSize = CalculationUtility::gdReduceFontSize($text, $font, $fontSize, $width);
        $this->addStacks([
            '-gravity center',
            '-font ' . $font,
            '-pointsize ' . $fontSize,
            '-fill "' . $shadowColor . '"',
            '-draw "text 1,1 \'' . $text . '\'"',
            '-fill "' . $textColor . '"',
            '-draw "text 0,0 \'' . $text . '\'"'
        ]);
        return $this;
    }

    /**
     * @param int $width
     * @param int $height
     * @param $text
     * @param string $font
     * @param int $fontSize
     * @param string $textColor
     * @param string $shadowColor
     * @return MagickUtility
     */
    public function textBottomLeft(int $width, int $height, $text, string $font, int $fontSize = 100, int $border = 0, string $textColor = '#ffffff', $shadowColor = '#7f7f7f'): MagickUtility {
        $fontSize = CalculationUtility::gdReduceFontSize($text, $font, $fontSize, $height);
        $fontSize += 4;

        if ($this->existsGraphicMagick) {
            $yText = $fontSize + $border;
        } else {
            $yText = $border;
        }

        $this->addStacks([
            '-pointsize ' . $fontSize,
            '-font ' . $font,

            '-rotate 90',
            '-gravity NorthWest',

            '-fill "' . $shadowColor . '"',
            '-draw "text ' . ($border + 5) . ',' . ($yText + 1) . ' \'' . $text . '\'"',

            '-fill "' . $textColor . '"',
            '-draw "text ' . ($border + 4) . ',' . $yText . ' \'' . $text . '\'"',

            '-rotate -90'
        ]);
        return $this;
    }

    /**
     * @param int $thickness
     * @param string $borderColor
     * @return MagickUtility
     */
    public function drawBorder($thickness = 1, $borderColor = '#ffffff'): MagickUtility {
        if ($thickness > 0) {
            $this->addStacks([
                '-shave ' . $thickness . 'x' . $thickness,
                '-bordercolor "' . $borderColor . '"',
                '-border ' . $thickness . 'x' . $thickness
            ]);
        }
        return $this;
    }

    /**
     * @param int $width
     * @param int $height Zero to aspect ratio
     * @return MagickUtility
     */
    public function thumbnailUncut(int $width, int $height = 0): MagickUtility {
        $this->addStacks([
            '-background transparent',
            '-coalesce',
            '-thumbnail ' . $width . ($height > 0 ? 'x' . $height : '')
        ]);
        return $this;
    }

    /**
     * @param int $width
     * @param int $height
     * @return MagickUtility
     */
    public function thumbnailCut(int $width, int $height): MagickUtility {
        $this->addStacks([
            '-background transparent',
            '-coalesce',
            '-thumbnail ' . $width . 'x' . $height . '^',
            '-gravity center',
            '-extent ' . $width . 'x' . $height
        ]);
        return $this;
    }

    protected function createCommandException() {
        throw new \Exception('Command not working: ' . PHP_EOL . $this->lastCommand);
    }

    /**
     * @param array $commands
     * @return bool
     */
    public function execute(): bool {
        $output = null;
        $return = null;

        $command = '';
        if (empty($commands)) {
            foreach ($this->stacks as $stack) {
                $command .= ' ' . implode(' ', $stack);
            }
            $command = trim($command);
        }

        if ($this->existsGraphicMagick()) {
            $command = $this->binaryGmagick . ' convert ' . $command;
        } else if ($this->existsImageMagick()) {
            $command = $this->binaryConvertImagick . ' ' . $command;
        }

        if ($this->isMagick()) {
            $this->lastCommand = $command;
            exec($command, $output, $return);
//            $return === 0 || $this->createCommandException(); // Debug
            return ($return === 0);
        }
        return false;
    }
}
