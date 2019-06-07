<?php
namespace App\Repository;

use App\Entity\Setting;
use Doctrine\ORM\EntityRepository;

/**
 * Class SettingRepository
 */
class SettingRepository extends EntityRepository {
    /**
     * Get only value from setting
     *
     * @param string $key
     * @return string
     */
    public function getSetting(string $key) {
        $setting = $this->findOneBy(['key' => $key]);
        if ($setting instanceof Setting) {
            return $setting->getValue();
        }
        return '';
    }
}
