<?php

namespace Helpcrunch\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Helpcrunch\Entity\HelpcrunchEntity;

/**
 * @method HelpcrunchEntity find(int $id)
 * @method HelpcrunchEntity findOneBy(array $criteria, array $orderBy = null)
 * @method HelpcrunchEntity[] findAll()
 * @method HelpcrunchEntity[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class HelpcrunchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return mixed
     */
    public function findEntities(int $offset, int $limit)
    {
        return $this->createQueryBuilder('a')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->useResultCache(false)
            ->setCacheable(false)
            ->getArrayResult();
    }
}
