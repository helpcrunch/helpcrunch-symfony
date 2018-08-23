<?php

namespace Helpcrunch\Validator\Constraints\UniqueValues;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueValue extends Constraint
{
    /**
     * @var string $entityClass
     */
    protected $entityClass;

    /**
     * @var string $message
     */
    public $message = 'Value is already in use.';

    public function __construct($entityClass, $options = null)
    {
        $this->entityClass = $entityClass;

        parent::__construct($options);
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}
