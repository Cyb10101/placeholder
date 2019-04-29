<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LicenceRepository")
 * @ORM\Table(name="licence")
 */
class Licence {
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
     * @var string
     * @ORM\Column(type="string", length=191, nullable=false)
     */
    private $title = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=191, nullable=false)
     */
    private $website = '';

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
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $title
     * @return self
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite(): string {
        return $this->website;
    }

    /**
     * @param string $website
     * @return self
     */
    public function setWebsite($website) {
        $this->website = $website;
        return $this;
    }
}
