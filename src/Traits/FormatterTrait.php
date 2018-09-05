<?php

namespace Helpcrunch\Traits;

trait FormatterTrait
{
    protected function convertUnderscoreToCamelCase(string $string): string
    {
        return lcfirst(implode('', array_map('ucfirst', explode('_', $string))));
    }

    protected function arrayKeysToCamelCase(array $data): array
    {
        foreach ($data as $key => $value) {
            $camelCaseKey = $this->convertUnderscoreToCamelCase($key);
            $data[$camelCaseKey] = $value;
        }

        return $data;
    }

    protected function getEntityClassname(string $entityClass): string
    {
        $classParts = explode('\\', $entityClass);

        return end($classParts);
    }
}
