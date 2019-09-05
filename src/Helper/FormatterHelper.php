<?php

namespace Helpcrunch\Helper;

class FormatterHelper
{
    public static function convertUnderscoreToCamelCase(string $string): string
    {
        return lcfirst(implode('', array_map('ucfirst', explode('_', $string))));
    }

    public static function convertCamelCaseToUnderscore(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    public static function clearString(string $string): string
    {
        return preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%\_\-@]/m', ' ', $string);
    }
}
