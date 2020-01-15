<?php

namespace Helpcrunch\Helper\DefaultEntities;

use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Doctrine\ORM\EntityManagerInterface;
use Helpcrunch\Entity\HelpcrunchEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractDefaultEntitiesHelper
{
    use HelpcrunchServicesTrait;

    /**
     * @var string
     */
    protected static $entityClass;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->entityManager = $this->getEntityManager();
    }

    public function create(): void
    {
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
