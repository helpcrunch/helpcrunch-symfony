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
     * @var bool $isNewEntity
     */
    protected $isNewEntity = true;

    /**
     * @var string $validatedField
     */
    protected $validatedField;

    /**
     * @var string $message
     */
    public $message = 'Value is already in use.';

    public function __construct(string $entityClass, bool $isNewEntity, string $validatedField = null, $options = null)
    {
        $this->entityClass = $entityClass;
        $this->isNewEntity = $isNewEntity;
        $this->validatedField = $validatedField;

        parent::__construct($options);
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function isNewEntity(): bool
    {
        return $this->isNewEntity;
    }

    public function getValidatedField()
    {
        return $this->validatedField;
    }
}
