<?php

namespace Helpcrunch\Validator\Constraints\UniqueValues;

use Symfony\Component\Validator\Constraint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @Annotation
 */
class UniqueValue extends Constraint
{
    /**
     * @var string $entityClass
     */
    protected $entityClass;

    public function __construct($entityClass, $options = null)
    {
        $this->entityClass = $entityClass;

        parent::__construct($options);
    }

    public $message = 'Value is already in use.';

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}
