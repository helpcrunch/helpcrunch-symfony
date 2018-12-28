<?php

namespace Helpcrunch\Entity;

trait GetterSetterTrait
{
    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if ($this->checkPropertyExists($name)) {
            return $this->$name;
        }

        return null;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function __set($name, $value)
    {
        if ($this->checkPropertyExists($name)) {
            $this->$name = $value;
        }

        return $this;
    }

    /**
     * @param string $propertyName
     * @return bool
     */
    protected function checkPropertyExists(string $propertyName)
    {
        if (!property_exists($this, $propertyName)) {
            throw new \Exception('Property ' . $propertyName . ' does not exist in ' . static::class);
        }

        return true;
    }
}
