<?php

namespace Helpcrunch\Entity;

trait GetterSetterTrait
{
    public function __get($name)
    {
        if ($this->checkPropertyExists($name)) {
            return $this->$name;
        }

        return null;
    }

    public function __set($name, $value): self
    {
        if ($this->checkPropertyExists($name)) {
            $this->$name = $value;
        }

        return $this;
    }

    protected function checkPropertyExists(string $propertyName): bool
    {
        if (!property_exists($this, $propertyName)) {
            throw new \Exception('Property ' . $propertyName . ' does not exist in ' . static::class);
        }

        return true;
    }
}
