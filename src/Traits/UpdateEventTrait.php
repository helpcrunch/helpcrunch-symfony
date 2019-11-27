<?php

namespace Helpcrunch\Traits;

use Helpcrunch\Entity\HelpcrunchEntity;

trait UpdateEventTrait
{
    protected function notifyFieldChanged(HelpcrunchEntity $entity, string $field, $oldValue, $newValue): void
    {
        $this->getEntityManager()
            ->getUnitOfWork()
            ->propertyChanged($entity, $field, $oldValue, $newValue);
    }
}
