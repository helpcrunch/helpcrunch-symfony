<?php

namespace Helpcrunch\Validator\ValidationRule;

class JoinColumnRule implements ValidationRuleInterface
{
    const RULE_VARIABLE = 'nullable';

    public function getRule(\ReflectionClass $class, object $context): bool
    {
        return $class->getProperty(self::RULE_VARIABLE)->getValue($context);
    }
}
