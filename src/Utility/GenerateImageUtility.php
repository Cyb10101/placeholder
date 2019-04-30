<?php
namespace App\Utility;

use App\Entity\Font;
use App\Entity\Format;
use App\Entity\Image;
use App\Repository\ImageRepository;
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
     * @var ImageRepository
     */
    protected $imageRepository = null;

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
        $this->imageRepository = $this->doctrine->getRepository(Image::class);
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
     * @param string $file
     * @return void
     * @todo In development
     */
    public function createImageFromFile($file) {
        $this->sourceFile = $file;
        $this->targetMime = FileUtility::getMimeTypeByFileExtension($file);

        $fileType = FileUtility::getMimeTypeByFilename($file);
        switch($fileType) {
            case 'image/jpeg': $this->image = imagecreatefromjpeg($file); break;
            case 'image/png': $this->image = imagecreatefrompng($file); break;
            case 'image/gif': $this->image = imagecreatefromgif($file); break;
            default:
                throw new \Exception('Create image from file failed!');
        }
    }

    /**
     * @return self
     * @todo In development
     */
    public function createImage() {
        $imageFile = null;
        /** @var \AppBundle\Entity\Image $image */
        $image = $this->imageRepository->findOneRandom($this->getCategory());
        $imageFile =  $this->pathImages . '/' . $image->getCategory() . '/' . $image->getFile();

        if ($imageFile !== null) {
            $this->createImageFromFile($imageFile);
            $this->shrinkAndCut($this->getWidth(), $this->getHeight());
        }



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
     * @param int $width
     * @param int $height
     * @return void
     * @todo In development
     */
    public function shrinkAndCut($width = 400, $height = 400) {
        $sourceWidth = imagesx($this->image);
        $sourceHeight = imagesy($this->image);

        $resize = CalculationUtility::calcShrinkBoxCover($sourceWidth, $sourceHeight, $width, $height);

        $shrinkImage = imagecreatetruecolor($resize->width, $resize->height);
        if ($shrinkImage !== false) {
            $this->setImageTransparent($shrinkImage, $this->targetMime);
            imagecopyresampled($shrinkImage, $this->image, 0, 0, 0, 0, $resize->width, $resize->height, $sourceWidth, $sourceHeight);

            $cutImage = imagecreatetruecolor($width, $height);
            if ($shrinkImage !== false) {
                $gap = CalculationUtility::calcGap($resize->width, $resize->height, $width, $height);
                $this->setImageTransparent($cutImage, $this->targetMime);
                imagecopyresampled($cutImage, $shrinkImage, 0, 0, $gap->width, $gap->height, $width, $height, $width, $height);

                imagedestroy($this->image);
                $this->image = $cutImage;
            }
            imagedestroy($shrinkImage);
        }
    }

    /**
     * @param resource $image
     * @param string $fileType
     * @return void
     * @todo In development
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

    // @todo Cache id for filenames
    public function getTextFilename(string $mimeType = 'image/jpeg'): string {
        $extension = FileUtility::getFileExtensionByMimeType($mimeType);
        return $this->type . '_' . $this->getWidth() . 'x' . $this->getHeight() . '_' . rand(1, 1000000) . '.' . $extension;
    }

    // @todo Cache id for filenames
    public function getImageFilename(string $mimeType = 'image/jpeg'): string {
        $extension = FileUtility::getFileExtensionByMimeType($mimeType);
        return $this->type . '_' . $this->getWidth() . 'x' . $this->getHeight() . '_' . rand(1, 1000000) . '.' . $extension;
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
