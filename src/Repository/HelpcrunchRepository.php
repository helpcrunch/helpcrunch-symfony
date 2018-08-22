<?php

namespace Helpcrunch\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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
