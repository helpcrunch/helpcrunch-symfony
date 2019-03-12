<?php

namespace Helpcrunch\Validator\ValidationRule;

interface ValidationRuleInterface
{
    public function getRule(\ReflectionClass $class, object $context);
}
