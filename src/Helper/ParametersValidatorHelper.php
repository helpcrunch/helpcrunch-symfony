<?php

namespace Helpcrunch\Helper;

class ParametersValidatorHelper
{
    public static function isNumericAndNotEmpty($value): bool
    {
        return !empty($value) && is_numeric($value);
    }

    public static function isStringAndNotEmpty($string): bool
    {
        return !empty($string) && is_string($string);
    }

    public static function isValuePresented($value, array $values): bool
    {
        return in_array($value, $values);
    }

    public static function isObject($value): bool
    {
        return is_object($value);
    }
}
