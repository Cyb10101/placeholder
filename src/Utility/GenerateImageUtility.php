<?php
namespace App\Utility;

use App\Utility\Generator\ImageConfiguration;

/**
 * Class GenerateImageUtility
 */
class GenerateImageUtility {
    /**
     * @var ImageConfiguration
     */
    protected $imageConfiguration = null;

    /**
     * @var string
     */
    protected $projectDirectory = '';

    /**
     * @var string
     */
    protected $pathFonts = 'public/fonts';

    /**
     * @var string
     */
    protected $pathImages = 'public/images/categories';

    /**
     * @var resource
     */
    protected $imageTarget = null;

    public function __construct(ImageConfiguration &$imageConfiguration, string $projectDirectory) {
        $this->imageConfiguration = &$imageConfiguration;
        $this->projectDirectory = $projectDirectory;
    }

    /**
     * @return self
     */
    public function createText() {
        if (!function_exists('imagecreate')) {
            throw new \Exception('Can\'t create new GD-Image-Stream!');
        }
        $this->imageTarget = @imagecreate($this->imageConfiguration->getWidth(), $this->imageConfiguration->getHeight());

        // Background
        $backgroundColor = ConvertUtility::colorHexToRgb($this->imageConfiguration->getBackgroundColor());
        if (!GeneralUtility::isColorRGB($backgroundColor)) {
            $backgroundColor = [200, 200, 200];
        }
        imagecolorallocate($this->imageTarget, $backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);

        return $this;
    }

    /**
     * @return self
     */
    public function createImage() {
        $imageFile = $this->projectDirectory . '/' . $this->pathImages . '/' . $this->imageConfiguration->getImage()->getCategory() . '/' . $this->imageConfiguration->getImage()->getFile();

        if ($imageFile !== null) {
            $this->createImageFromFile($imageFile);
            $this->shrinkAndCut($this->imageConfiguration->getWidth(), $this->imageConfiguration->getHeight());
        }
        return $this;
    }

    /**
     * @param string $file
     * @return void
     */
    public function createImageFromFile($file) {
        $this->imageConfiguration->setMimeType(FileUtility::getMimeTypeByFileExtension($file));

        $fileType = FileUtility::getMimeTypeByFilename($file);
        switch($fileType) {
            case 'image/jpeg': $this->imageTarget = imagecreatefromjpeg($file); break;
            case 'image/png': $this->imageTarget = imagecreatefrompng($file); break;
            case 'image/gif': $this->imageTarget = imagecreatefromgif($file); break;
            default:
                throw new \Exception('Create image from file failed!');
        }
    }

    /**
     * @param int $width
     * @param int $height
     * @return void
     */
    public function shrinkAndCut($width = 400, $height = 400) {
        $sourceWidth = imagesx($this->imageTarget);
        $sourceHeight = imagesy($this->imageTarget);

        $resize = CalculationUtility::calcShrinkBoxCover($sourceWidth, $sourceHeight, $width, $height);

        $shrinkImage = imagecreatetruecolor($resize->width, $resize->height);
        if ($shrinkImage !== false) {
            $this->setImageTransparent($shrinkImage, $this->imageConfiguration->getMimeType());
            imagecopyresampled($shrinkImage, $this->imageTarget, 0, 0, 0, 0, $resize->width, $resize->height, $sourceWidth, $sourceHeight);

            $cutImage = imagecreatetruecolor($width, $height);
            if ($shrinkImage !== false) {
                $gap = CalculationUtility::calcGap($resize->width, $resize->height, $width, $height);
                $this->setImageTransparent($cutImage, $this->imageConfiguration->getMimeType());
                imagecopyresampled($cutImage, $shrinkImage, 0, 0, $gap->width, $gap->height, $width, $height, $width, $height);

                imagedestroy($this->imageTarget);
                $this->imageTarget = $cutImage;
            }
            imagedestroy($shrinkImage);
        }
    }

    /**
     * @param resource $image
     * @param string $fileType
     * @return void
     */
    protected function setImageTransparent(&$image, $fileType) {
        if ($fileType === 'image/png' || $fileType === 'image/gif') {
            imagealphablending($image, false);
            imagesavealpha($image, true);

            $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $color);
        }
    }

    /**
     * @return self
     */
    public function generateImage() {
        // Draw text
        if (!empty($this->imageConfiguration->getText())) {
            $fontFile = $this->projectDirectory . '/' . $this->pathFonts . '/' . $this->imageConfiguration->getFont()->getFile();
            $textColor = ConvertUtility::colorHexToRgb($this->imageConfiguration->getForegroundColor());
            $shadowColor = ConvertUtility::colorHexToRgb(ConvertUtility::colorHexAdjustBrightness($this->imageConfiguration->getForegroundColor(), -50));

            if ($this->imageConfiguration->getPosition() === 'vertical-left') {
                $this->textBottomLeft($this->imageConfiguration->getText(), $fontFile, 100, $textColor, $shadowColor);
            } else {
                $this->textCenter($this->imageConfiguration->getText(), $fontFile, 100, $textColor, $shadowColor);
            }
        }

        // Draw border
        if ($this->imageConfiguration->getBorder() > 0) {
            $this->drawBorder($this->imageConfiguration->getBorder(), ConvertUtility::colorHexToRgb($this->imageConfiguration->getForegroundColor()));
        }

        return $this;
    }

    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function saveImage(string $path): bool {
        $filename = $path . '/' . $this->imageConfiguration->getCacheFilename();

        switch($this->imageConfiguration->getMimeType()) {
            case 'image/jpeg': imagejpeg($this->imageTarget, $filename, 100); break;
            case 'image/png': imagepng($this->imageTarget, $filename); break;
            case 'image/gif': imagegif($this->imageTarget, $filename); break;
            default:
                throw new \Exception('Save image failed!');
        }
        return true;
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

        $sourceWidth = imagesx($this->imageTarget);
        $sourceHeight = imagesy($this->imageTarget);

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
        $color = imagecolorallocate($this->imageTarget, $shadowColor[0], $shadowColor[1], $shadowColor[2]);
        imagettftext($this->imageTarget, $fontSize, 0, ($x + 1), ($y + 1), $color, $font, $text);

        // Text
        $color = imagecolorallocate($this->imageTarget, $textColor[0], $textColor[1], $textColor[2]);
        imagettftext($this->imageTarget, $fontSize, 0, $x, $y, $color, $font, $text);
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

        $sourceHeight = imagesy($this->imageTarget);

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
        $color = imagecolorallocate($this->imageTarget, $shadowColor[0], $shadowColor[1], $shadowColor[2]);
        imagettftext($this->imageTarget, $fontSize, 90, ($x + 11), ($sourceHeight - 11), $color, $font, $text);

        // Text
        $color = imagecolorallocate($this->imageTarget, $textColor[0], $textColor[1], $textColor[2]);
        imagettftext($this->imageTarget, $fontSize, 90, ($x + 10), ($sourceHeight - 10), $color, $font, $text);
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
        $x2 = imagesx($this->imageTarget) - 1;
        $y2 = imagesy($this->imageTarget) - 1;

        $color = imagecolorallocate($this->imageTarget, $borderColor[0], $borderColor[1], $borderColor[2]);
        for ($i = 0; $i < $thickness; $i++) {
            imagerectangle($this->imageTarget, $x1++, $y1++, $x2--, $y2--, $color);
        }
    }
}
