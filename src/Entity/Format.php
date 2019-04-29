<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormatRepository")
 * @ORM\Table(name="format")
 */
class Format {
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id = 0;

    /**
     * @var string
     * @ORM\Column(type="string", length=191, nullable=false)
     */
    private $key = '';

    /**
     * @var int
     * @ORM\Column(type="integer", length=10, options={"default": 0, "unsigned": true})
     */
    private $width = 0;

    /**
     * @var int
     * @ORM\Column(type="integer", length=10, options={"default": 0, "unsigned": true})
     */
    private $height = 0;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string {
        return $this->key;
    }

    /**
     * @param string $key
     * @return self
     */
    public function setKey($key) {
        $this->key = $key;
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
        $this->width = $width;
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
        $this->height = $height;
        return $this;
    }

}
