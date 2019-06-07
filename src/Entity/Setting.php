<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SettingRepository")
 * @ORM\Table(name="setting")
 */
class Setting {
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
     * @ORM\Column(type="string", length=191, nullable=true, options={"default": ""})
     */
    private $value = '';

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
    public function getValue(): string {
        return $this->value;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }
}
