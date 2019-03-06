<?php

namespace Helpcrunch\Validator\ValidationRule;

class RelationRule implements ValidationRuleInterface
{
    const RULE_VARIABLE = 'targetEntity';

    /**
     * @param \ReflectionClass $class
     * @param object $context
     * @return mixed
     */
    public function getRule(\ReflectionClass $class, object $context)
    {
        $targetEntityProperty = $class->getProperty(self::RULE_VARIABLE);
        $targetEntityProperty->setAccessible(true);
        $targetEntity = $targetEntityProperty->getValue($context);

        return $targetEntity;
    }
}
