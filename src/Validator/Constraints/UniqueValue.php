<?php

namespace Helpcrunch\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @property string $message
 * @property string|null $entityClass
 * @property string|null $field
 */
class UniqueValue extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The string "{{ value }}" already exists';

    /**
     * @var null|string
     */
    public $entityClass = null;

    /**
     * @var null|string
     */
    public $field = null;
}
