<?php

namespace Helpcrunch\Validator\Constraints;

use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueValueValidator extends ConstraintValidator
{
    use HelpcrunchServicesTrait;

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof UniqueValue)) {
            throw new UnexpectedTypeException($constraint, UniqueValue::class);
        }

        if (is_null($constraint->entityClass) || is_null($constraint->field)) {
            throw new UnexpectedValueException(null, 'string');
        }

        $repository = $this->getEntityManager()->getRepository($constraint->entityClass);
        if ($repository->findOneBy([$constraint->field => $value])) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
