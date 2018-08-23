<?php

namespace Helpcrunch\Validator\Constraints\UniqueValues;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

abstract class AbstractUniqueValueValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value, UniqueValue $constraint)
    {
        if ($value === null || $value == '') {
            throw new UnexpectedTypeException('Value can not be empty.', 'string');
        }

        if ($this->checkValueExists($value, $constraint)) {
            $this->context->buildViolation($constraint->message)
                ->atPath($this->getPath())
                ->addViolation();
        }
    }

    abstract protected function checkValueExists(string $value, Constraint $constraint);

    abstract protected function getPath(): string;
}
