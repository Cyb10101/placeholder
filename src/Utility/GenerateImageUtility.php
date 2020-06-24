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
    protected $cacheDirectory = '';

    /**
     * @var string
     */
    protected $pathFonts = 'public/fonts';

    /**
     * @var string
     */
    protected $pathImages = 'public/images/categories';

    /**
     * @var MagickUtility
     */
    protected $magickUtility = null;

    public function __construct(ImageConfiguration &$imageConfiguration, string $projectDirectory, string $cacheDirectory) {
        $this->imageConfiguration = &$imageConfiguration;
        $this->projectDirectory = $projectDirectory;
        $this->cacheDirectory = $cacheDirectory;

        $this->magickUtility = MagickUtility::getInstance();
        if (!$this->magickUtility->isMagick()) {
            throw new \Exception('Magick not found');
        }
    }

    /**
     * @return string
     */
    public function getCachedFilename(): string {
        return $this->cacheDirectory . '/' . $this->imageConfiguration->getCacheFilename();
    }

    /**
     * @return self
     */
    public function createText() {
        $this->magickUtility->createCanvas(
            $this->getCachedFilename(),
            $this->imageConfiguration->getWidth(), $this->imageConfiguration->getHeight(),
            $this->imageConfiguration->getBackgroundColor()
        );
        $this->magickUtility->quality(100);

        $this->drawText();

        $this->magickUtility
            ->drawBorder($this->imageConfiguration->getBorder(), $this->imageConfiguration->getForegroundColor())
            ->addOutputFile($this->getCachedFilename())
            ->execute();
        return $this;
    }

    /**
     * @return self
     */
    public function createImage() {
        $image = $this->imageConfiguration->getImage();
        $imageFile = $this->projectDirectory . '/' . $this->pathImages . '/' . $image->getCategory() . '/' . $image->getFile();

        if ($imageFile !== null) {
            $this->magickUtility
                ->addInputFile($imageFile)
                ->quality(100)
                ->thumbnailCut($this->imageConfiguration->getWidth(), $this->imageConfiguration->getHeight());

            $this->drawText();

            $this->magickUtility
                ->drawBorder($this->imageConfiguration->getBorder(), $this->imageConfiguration->getForegroundColor())
                ->addOutputFile($this->getCachedFilename())
                ->execute();
        }
        return $this;
    }

    /**
     * @return self
     */
    protected function drawText() {
        if (!empty($this->imageConfiguration->getText())) {
            $fontFile = $this->projectDirectory . '/' . $this->pathFonts . '/' . $this->imageConfiguration->getFont()->getFile();
            $textColor = $this->imageConfiguration->getForegroundColor();
            $shadowColor = ConvertUtility::colorHexAdjustBrightness($this->imageConfiguration->getForegroundColor(), -50);

            if ($this->imageConfiguration->getPosition() === 'vertical-left') {
                $this->magickUtility->textBottomLeft(
                    $this->imageConfiguration->getWidth(), $this->imageConfiguration->getHeight(),
                    $this->imageConfiguration->getText(), $fontFile, 100,
                    $this->imageConfiguration->getBorder(), $textColor, $shadowColor
                );
            } else {
                  $this->magickUtility->textCenter(
                    $this->imageConfiguration->getWidth(), $this->imageConfiguration->getHeight(),
                    $this->imageConfiguration->getText(), $fontFile, 100, $textColor, $shadowColor
                );
            }
        }
        return $this;
    }

    /**
     * @return self
     */
    protected function drawBorder() {
        return $this;
    }
}
