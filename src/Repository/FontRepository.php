<?php
namespace App\Repository;

use App\Entity\Font;
use Doctrine\ORM\EntityRepository;

/**
 * Class FontRepository
 */
class FontRepository extends EntityRepository {
    /**
     * @return Font
     */
    public function findOneRandom(): Font {
        $query = $this->createQueryBuilder('t');
        return $query->addOrderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }
}
