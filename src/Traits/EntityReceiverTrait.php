<?php

namespace Helpcrunch\Traits;

use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Exception\EntityNotFoundException;
use Helpcrunch\Exception\InvalidParameterException;
use Helpcrunch\Helper\ParametersValidatorHelper;

trait EntityReceiverTrait
{
    protected function getEntity($id, string $entityClass): HelpcrunchEntity
    {
        if (!ParametersValidatorHelper::isValidId($id)) {
            throw new InvalidParameterException('id');
        }
        /** @var HelpcrunchEntity $entity */
        $entity = $this->getDoctrine()->getRepository($entityClass)->find($id);
        if (!$entity) {
            throw new EntityNotFoundException($class);
        }

        return $entity;
    }
}
