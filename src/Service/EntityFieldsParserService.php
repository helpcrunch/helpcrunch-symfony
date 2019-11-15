<?php

namespace App\Service;

use DateTime;

class EntityFieldsParserService
{
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function parse(array $fields): array
    {
        return array_map([$this, 'checkValue'], $fields);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function checkValue($value)
    {
        if ($value instanceof DateTime) {
            $value = $this->updateDateTimeValue($value);
        }

        return $value;
    }

    private function updateDateTimeValue(DateTime $value, string $format = null): string
    {
        return $value->format($format ?? self::DATETIME_FORMAT);
    }
}
