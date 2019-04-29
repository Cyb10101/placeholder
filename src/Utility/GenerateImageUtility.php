<?php
namespace App\Utility;

use App\Entity\Font;
use App\Entity\Format;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Class GenerateImageUtility
 */
class GenerateImageUtility {
    use \App\Traits\ImageConfigurationTrait;

    /**
     * @var Registry
     */
    protected $doctrine = null;

    /**
     * @var string
     */
    protected $projectDirectory = '';

    /**
     * @var string
     */
    protected $pathFonts = 'public/fonts';

    public function __construct(Registry $doctrine, string $projectDirectory) {
        $this->doctrine = $doctrine;
        $this->projectDirectory = $projectDirectory;

        $this->fontRepository = $this->doctrine->getRepository(Font::class);
        $this->formatRepository = $this->doctrine->getRepository(Format::class);
    }

    /**
     * @return self
     */
    public function createText() {
        if (!function_exists('imagecreate')) {
            throw new \Exception('Can\'t create new GD-Image-Stream!');
        }
        $this->image = @imagecreate($this->getWidth(), $this->getHeight());

        // Background
        $backgroundColor = ConvertUtility::colorHexToRgb($this->getBackgroundColor());
        if (!GeneralUtility::isColorRGB($backgroundColor)) {
            $backgroundColor = [200, 200, 200];
        }
        imagecolorallocate($this->image, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);

        return $this;
    }

    /**
     * @return self
     */
    public function generateImage() {
        // Set text
        if (empty($this->getText())) {
            if ($this->isForegroundColorChanged() || $this->getType() !== 'image' || $this->isPositionChanged()) {
                $this->setText($this->getWidth() . 'x' . $this->getHeight());
            }
        }

        // Draw text
        if (!empty($this->getText())) {
            $fontFile = $this->projectDirectory . '/' . $this->pathFonts . '/' . $this->getFont()->getFile();
            $textColor = ConvertUtility::colorHexToRgb($this->getForegroundColor());
            $shadowColor = ConvertUtility::colorHexToRgb(ConvertUtility::colorHexAdjustBrightness($this->getForegroundColor(), -50));

            if ($this->getPosition() === 'vertical-left') {
                $this->textBottomLeft($this->getText(), $fontFile, 100, $textColor, $shadowColor);
            } else {
                $this->textCenter($this->getText(), $fontFile, 100, $textColor, $shadowColor);
            }
        }

        // Draw border
        if ($this->getBorder() > 0) {
            $this->drawBorder($this->getBorder(), ConvertUtility::colorHexToRgb($this->getForegroundColor()));
        }

        return $this;
    }

    /**
     * @param string $path
     * @return string
     * @throws \Exception
     */
    public function saveImage(string $path): string {
        if ($this->getType() === 'image') {
            $file = $this->getTextFilename($this->mimeType);
        } else {
            $file = $this->getTextFilename($this->mimeType);
        }

        switch($this->mimeType) {
            case 'image/jpeg': imagejpeg($this->image, $path . '/' . $file, 100); break;
            case 'image/png': imagepng($this->image, $path . '/' . $file); break;
            case 'image/gif': imagegif($this->image, $path . '/' . $file); break;
            default:
                throw new \Exception('Save image failed!');
        }
        return $path . '/' . $file;
    }

    public function getTextFilename(string $mimeType = 'image/jpeg'): string {
        $extension = FileUtility::getFileExtensionByMimeType($mimeType);
        return $this->type . '_' . $this->getWidth(). 'x' . $this->getHeight(). '.' . $extension;
    }

    public function getImageFilename(string $mimeType = 'image/jpeg'): string {
        $extension = FileUtility::getFileExtensionByMimeType($mimeType);
        return $this->type . '_' . $this->getWidth(). 'x' . $this->getHeight(). '.' . $extension;
    }


    /**
     * @param string $text
     * @param string $font
     * @param int $fontSize
     * @param array $textColor
     * @param array $shadowColor
     * @return void
     */
    public function textCenter($text, $font, $fontSize = 100, $textColor = [80, 80, 80], $shadowColor = [220, 220, 220]) {
        if (!GeneralUtility::isColorRGB($textColor) || !GeneralUtility::isColorRGB($shadowColor)) {
            return;
        }

        $sourceWidth = imagesx($this->image);
        $sourceHeight = imagesy($this->image);

        $testSize = true;
        do {
            $fontBox = imagettfbbox($fontSize, 0, $font, $text);
            $padding = 40;
            if ($padding + ($fontBox[4] - $fontBox[0]) < $sourceWidth) {
                $testSize = false;
            } else {
                $fontSize--;
            }

            if ($fontSize === 1) {
                return;
            }
        } while ($testSize === true);

        $fontBox = imagettfbbox($fontSize, 0, $font, $text);
        $widthAscent = abs($fontBox[0]);
        $widthDescent = abs($fontBox[2]);
        $heightAscent = abs($fontBox[7]);
        $heightDescent = abs($fontBox[1]);
        $width = $widthAscent + $widthDescent;
        $height = $heightAscent + $heightDescent;

        $x = ($sourceWidth / 2) - ($width / 2) + $widthAscent;
        $y = (($sourceHeight / 2) - ($height / 2)) + $heightAscent;

        // Shaddow
        $color = imagecolorallocate($this->image, $shadowColor[0], $shadowColor[1], $shadowColor[2]);
        imagettftext($this->image, $fontSize, 0, ($x + 1), ($y + 1), $color, $font, $text);

        // Text
        $color = imagecolorallocate($this->image, $textColor[0], $textColor[1], $textColor[2]);
        imagettftext($this->image, $fontSize, 0, $x, $y, $color, $font, $text);
    }

    /**
     * @param string $text
     * @param string $font
     * @param int $fontSize
     * @param array $textColor
     * @param array $shadowColor
     * @return void
     */
    public function textBottomLeft($text, $font, $fontSize = 100, $textColor = [80, 80, 80], $shadowColor = [220, 220, 220]) {
        if (!GeneralUtility::isColorRGB($textColor) || !GeneralUtility::isColorRGB($shadowColor)) {
            return;
        }

        $sourceHeight = imagesy($this->image);

        $testSize = true;
        do {
            $fontBox = imagettfbbox($fontSize, 90, $font, $text);
            if (15 + (($fontBox[5] * -1) - $fontBox[1]) < $sourceHeight) {
                $testSize = false;
            } else {
                $fontSize--;
            }

            if ($fontSize === 1) {
                return;
            }
        } while ($testSize === true);

        $fontBox = imagettfbbox($fontSize, 90, $font, $text);
        $x = ($fontBox[4] * -1);

        // Shaddow
        $color = imagecolorallocate($this->image, $shadowColor[0], $shadowColor[1], $shadowColor[2]);
        imagettftext($this->image, $fontSize, 90, ($x + 11), ($sourceHeight - 11), $color, $font, $text);

        // Text
        $color = imagecolorallocate($this->image, $textColor[0], $textColor[1], $textColor[2]);
        imagettftext($this->image, $fontSize, 90, ($x + 10), ($sourceHeight - 10), $color, $font, $text);
    }

    /**
     * @param int $thickness
     * @param array $borderColor
     * @return void
     */
    public function drawBorder($thickness = 1, $borderColor = [0, 0 , 0]) {
        if (!GeneralUtility::isColorRGB($borderColor)) {
            $borderColor = [0, 0 , 0];
        }

        $x1 = 0;
        $y1 = 0;
        $x2 = imagesx($this->image) - 1;
        $y2 = imagesy($this->image) - 1;

        $color = imagecolorallocate($this->image, $borderColor[0], $borderColor[1], $borderColor[2]);
        for ($i = 0; $i < $thickness; $i++) {
            imagerectangle($this->image, $x1++, $y1++, $x2--, $y2--, $color);
        }
    }
}
