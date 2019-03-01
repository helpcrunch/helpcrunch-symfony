<?php

namespace Helpcrunch\Helper;

class ParametersValidatorHelper
{
    public static function isValidId($value): bool
    {
        return !empty($value) && is_numeric($value) && ($value > 0);
    }
}
