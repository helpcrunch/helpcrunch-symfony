<?php

namespace Helpcrunch\Helper\DefaultEntities;

use Doctrine\ORM\EntityManagerInterface;
use Helpcrunch\Entity\HelpcrunchEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractDefaultEntitiesHelper
{
    /**
     * @var string
     */
    protected static $entityClass;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    public function create(): void
    {
        $this->entityManager = $this->container->get('doctrine')->getEntityManager();

        foreach (static::getEntitiesData() as $entityData) {
            if (!class_exists(static::$entityClass)) {
                return;
            }

            $entity = new static::$entityClass;
            $this->fillEntity($entity, $entityData);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }
    }

    abstract protected function getEntitiesData(): array;

    abstract protected function fillEntity(HelpcrunchEntity $entity, array $entityData): void;
}
