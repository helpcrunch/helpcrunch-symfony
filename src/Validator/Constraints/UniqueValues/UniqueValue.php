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
     * @var bool|int $entityId
     */
    protected $entityId = false;

    /**
     * @var string $validatedField
     */
    protected $validatedField;

    /**
     * @var string $message
     */
    public $message = 'Value is already in use.';

    public function __construct(
        string $entityClass,
        bool $entityId,
        string $validatedField,
        $options = null
    ) {
        $this->entityClass = $entityClass;
        $this->entityId = $entityId;
        $this->validatedField = $validatedField;

        parent::__construct($options);
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getEntityId()
    {
        return $this->entityId;
    }

    public function getValidatedField(): string
    {
        return $this->validatedField;
    }
}
