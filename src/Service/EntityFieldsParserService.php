<?php

namespace Helpcrunch\Service;

use DateTime;
use Helpcrunch\Entity\HelpcrunchEntity;

class EntityFieldsParserService
{
    public const DATETIME_FORMAT = 'U.v';

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

        if ($value instanceof HelpcrunchEntity) {
            $value = $value->id;
        }

        return $value;
    }

    private function updateDateTimeValue(DateTime $value, string $format = null): string
    {
        return $value->format($format ?? self::DATETIME_FORMAT);
    }
}
