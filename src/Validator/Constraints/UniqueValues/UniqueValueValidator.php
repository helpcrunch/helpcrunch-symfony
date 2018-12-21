<?php

namespace Helpcrunch\Validator\Constraints\UniqueValues;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueValueValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface $entityManager
     */
    protected $entityManager;

    /**
     * @var null|string $validatedField
     */
    protected $validatedField;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value, Constraint $constraint)
    {
        if (empty($value) && (!method_exists($constraint, 'isNewEntity') || $constraint->isNewEntity())) {
            throw new UnexpectedTypeException('Value can not be empty.', 'string');
        }

        if ($this->checkValueExists($value, $constraint) && $constraint->isNewEntity()) {
            $this->context->buildViolation($constraint->message)
                ->atPath($constraint->getValidatedField())
                ->addViolation();
        }
    }

    protected function checkValueExists(string $value, Constraint $constraint)
    {
        if ((!$constraint instanceof UniqueValue)) {
            throw new UnexpectedTypeException($constraint, UniqueValue::class);
        }

        return $this->entityManager
            ->getRepository($constraint->getEntityClass())
            ->findOneBy([$constraint->getValidatedField() => $value]);
    }
}
