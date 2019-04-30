<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 * @ORM\Table(name="image")
 */
class Image {
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
	private $category = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=191, nullable=false)
     */
    private $file = '';

    /**
     * @var Licence
     * @ORM\ManyToOne(targetEntity="App\Entity\Licence")
     * @ORM\JoinColumn(name="licence", referencedColumnName="id")
     */
    private $licence = null;

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
    public function getCategory(): string {
        return $this->category;
    }

    /**
     * @param string $category
     * @return self
     */
    public function setCategory($category) {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getFile(): string {
        return $this->file;
    }

    /**
     * @param string $file
     * @return self
     */
    public function setFile($file) {
        $this->file = $file;
        return $this;
    }

    /**
     * @return Licence
     */
    public function getLicence(): Licence {
        return $this->licence;
    }

    /**
     * @param Licence $licence
     * @return self
     */
    public function setLicence($licence) {
        $this->licence = $licence;
        return $this;
    }
}
