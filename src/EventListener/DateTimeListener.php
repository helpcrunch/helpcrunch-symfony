<?php

namespace Helpcrunch\EventListener;

use DateTime;
use Helpcrunch\Entity\HelpcrunchEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DateTimeListener
{
    const CREATED_AT_FIELD = 'createdAt';
    const UPDATED_AT_FIELD = 'updatedAt';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function prePersist(HelpcrunchEntity $entity): void
    {
        if (property_exists($entity, self::CREATED_AT_FIELD)) {
            $entity->{self::CREATED_AT_FIELD} = new DateTime();
        }

        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->{self::UPDATED_AT_FIELD} = new DateTime();
        }
    }

    public function preUpdate(HelpcrunchEntity $entity): void
    {
        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->{self::UPDATED_AT_FIELD} = new DateTime();
        }
    }
}
