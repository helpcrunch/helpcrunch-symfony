<?php

namespace Helpcrunch\Validator\ValidationRule;

use Symfony\Component\Validator\Constraint;

class AssertionRule implements ValidationRuleInterface
{
    /**
     * @param \ReflectionClass $class
     * @param object $context
     * @return bool|Constraint
     */
    public function getRule(\ReflectionClass $class, object $context)
    {
        $validatorParameters = get_object_vars($context);
        try {
            /** @var Constraint $validator */
            $validator = $class->newInstance($validatorParameters);

            return $validator;
        } catch (\Throwable $exception) {
            return false;
        }
    }
}
