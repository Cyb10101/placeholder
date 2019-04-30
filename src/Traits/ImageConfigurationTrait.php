<?php
namespace App\Traits;

use App\Entity\Font;
use App\Entity\Format;
use App\Entity\Image;
use App\Repository\FontRepository;
use App\Repository\FormatRepository;
use App\Repository\ImageRepository;
use App\Utility\FileUtility;
use App\Utility\GeneralUtility;
use App\Utility\RequestUtility;

/**
 * Trait ImageConfigurationTrait
 */
trait ImageConfigurationTrait {
    /**
     * @var FontRepository
     */
    protected $fontRepository = null;

    /**
     * @var FormatRepository
     */
    protected $formatRepository = null;



    /**
     * @var resource
     */
    protected $image = null;

    /**
     * @var string
     */
    protected $mimeType = 'image/jpeg';

    /**
     * @var string
     */
    protected $type = 'text';

    /**
     * @var int
     */
    protected $width = 300;

    /**
     * @var int
     */
    protected $height = 300;

    /**
     * @var string
     */
    protected $text = '';

    /**
     * @var string
     */
    protected $category = '';

    /**
     * @var string
     */
    protected $backgroundColor = '#C8C8C8';

    /**
     * @var string
     */
    protected $foregroundColor = '#505050';

    /**
     * @var bool
     */
    protected $foregroundColorChanged = false;

    /**
     * @var int
     */
    protected $border = 0;

    /**
     * @var string
     */
    protected $position = 'center';

    /**
     * @var boolean
     */
    protected $positionChanged = false;

    /**
     * @var Font
     */
    protected $font = null;

    /**
     * @return string
     */
    public function getMimeType(): string {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     * @return self
     */
    public function setMimeType($mimeType) {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * @param string $extension
     * @return self
     */
    public function setMimeTypeByExtension(string $extension) {
        if (!empty($extension)) {
            $fileType = FileUtility::getMimeTypeByFileExtension('file.' . $extension);
            if (in_array($fileType, ['image/jpeg', 'image/png', 'image/gif'])) {
                $this->mimeType = $fileType;
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @param string $type
     * @return self
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth(): int {
        return $this->width;
    }

    /**
     * @param int $width
     * @return self
     */
    public function setWidth($width) {
        $this->width = max(16, min(2048, (int)$width));
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): int {
        return $this->height;
    }

    /**
     * @param int $height
     * @return self
     */
    public function setHeight($height) {
        $this->height = max(16, min(2048, (int)$height));
        return $this;
    }

    /**
     * @param string $format
     * @return self
     */
    public function setFormat(string $format) {
        if (!empty($format)) {
            if (preg_match('/^([0-9]{2,4})x([0-9]{2,4})$/i', $format, $match)) {
                $this->setWidth($match[1])->setHeight($match[2]);
            } else if (preg_match('/^([0-9]{2,4})$/i', $format)) {
                $this->setWidth($format)->setHeight($format);
            } else if (preg_match('/^([A-Za-z0-9]+)$/i', $format, $match)) {
                /** @var Format $format */
                $format = $this->formatRepository->findOneBy(['key' => $format]);
                if ($format instanceof Format) {
                    $this->setWidth($format->getWidth())->setHeight($format->getHeight());
                }
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string {
        return $this->text;
    }

    /**
     * @param string $text
     * @return self
     */
    public function setText($text) {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string {
        return $this->category;
    }

    /**
     * @param string $category
     * @return self
     */
    public function setCategory($category) {
        if (in_array($category, $this->imageRepository->getCategories())) {
            $this->category = $category;
        } else {
            $this->setText('! Category not found !');
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getBackgroundColor(): string {
        return $this->backgroundColor;
    }

    /**
     * @param string $backgroundColor
     * @return self
     */
    public function setBackgroundColor($backgroundColor) {
        $color = $this->getType() === 'text' ? '#C8C8C8' : '#505050';
        $hexColor = RequestUtility::filterHexColor($backgroundColor, $color);
        if ($hexColor !== null) {
            $this->backgroundColor = $hexColor;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getForegroundColor(): string {
        return $this->foregroundColor;
    }

    /**
     * @param string $foregroundColor
     * @return self
     */
    public function setForegroundColor($foregroundColor) {
        $color = $this->getType() === 'text' ? '#505050' : '#C8C8C8';
        $hexColor = RequestUtility::filterHexColor($foregroundColor, $color);
        if ($hexColor !== null) {
            $this->foregroundColorChanged = true;
            $this->foregroundColor = $hexColor;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isForegroundColorChanged(): bool {
        return $this->foregroundColorChanged;
    }

    /**
     * @param bool $foregroundColorChanged
     * @return self
     */
    public function setForegroundColorChanged($foregroundColorChanged) {
        $this->foregroundColorChanged = $foregroundColorChanged;
        return $this;
    }

    /**
     * @return int
     */
    public function getBorder(): int {
        return $this->border;
    }

    /**
     * @param int $border
     * @return self
     */
    public function setBorder($border) {
        $this->border = max(0, min(16, $border));
        return $this;
    }

    /**
     * @return string
     */
    public function getPosition(): string {
        return $this->position;
    }

    /**
     * @param string $position
     * @return self
     */
    public function setPosition($position) {
        if (!empty($position) && in_array($position, ['center', 'vertical-left'])) {
            $this->position = $position;
            $this->positionChanged = true;
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isPositionChanged(): bool {
        return $this->positionChanged;
    }

    /**
     * @param bool $positionChanged
     * @return self
     */
    public function setPositionChanged($positionChanged) {
        $this->positionChanged = $positionChanged;
        return $this;
    }

    /**
     * @return Font
     */
    public function getFont(): Font {
        if (empty($this->font)) {
            $this->font = $this->fontRepository->findOneRandom();
        }
        return $this->font;
    }

    /**
     * @param Font $font
     * @return self
     */
    public function setFont($font) {
        if (empty($font)) {
            $this->font = $this->fontRepository->findOneRandom();
        } else {
            /** @var Font $font */
            $font = $this->fontRepository->findOneBy(['key' => $font]);
            if (!$font instanceof Font) {
                $font = $this->fontRepository->findOneRandom();
                $this->setText('! Font not found !');
            }
            $this->font = $font;
        }
        return $this;
    }
}
