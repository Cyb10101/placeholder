<?php
namespace App\Repository;

use App\Entity\Image;
use Doctrine\ORM\EntityRepository;

/**
 * Class ImageRepository
 */
class ImageRepository extends EntityRepository {
    /**
     * @param string $category
     * @return Image
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneRandom(string $category = ''): Image {
        $query = $this->createQueryBuilder('t');
        if (!empty($category)) {
            $query->where('t.category = :category')
                ->setParameter('category', $category);
        }
        return $query->addOrderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Get image categories
     * @return array
     */
    public function getCategories()
    {
        $rows = $this->createQueryBuilder('t')
            ->select('t.category')
            ->distinct(true)
            ->getQuery()
            ->getScalarResult();

        $categories = [];
        foreach ($rows as $row) {
            $categories[] = $row['category'];
        }
        return $categories;
    }
}
