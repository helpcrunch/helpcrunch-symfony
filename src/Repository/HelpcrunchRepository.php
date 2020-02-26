<?php

namespace Helpcrunch\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Helpcrunch\Entity\HelpcrunchEntity;

/**
 * @method HelpcrunchEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method HelpcrunchEntity[] findAll()
 * @method HelpcrunchEntity[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
abstract class HelpcrunchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    public function setEntityManager(EntityManagerInterface $entityManager): self
    {
        $this->_em = $entityManager;

        return $this;
    }

    /**
     * @param int $id
     * @param int|null $lockMode
     * @param int|null $lockVersion
     * @return HelpcrunchEntity|object|null
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        if (!$id || !is_numeric($id) || ($id < 0)) {
            return null;
        }

        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return HelpcrunchEntity[]
     */
    public function findEntities(int $offset, int $limit)
    {
        return $this->createQueryBuilder('a')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->useResultCache(false)
            ->setCacheable(false)
            ->getResult();
    }

    public function count(array $criteria = []): int
    {
        return parent::count($criteria);
    }
}
