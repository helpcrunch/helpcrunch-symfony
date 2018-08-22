<?php

namespace Helpcrunch\Entity;

trait GetterSetterTrait
{
    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if ($this->checkPropertyExists($name)) {
            return $this->$name;
        }
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        if ($this->checkPropertyExists($name)) {
            $this->$name = $value;

            return $this;
        }
    }

    /**
     * @param string $propertyName
     * @return bool
     * @throws \Exception
     */
    protected function checkPropertyExists(string $propertyName): bool
    {
        if (!property_exists($this, $propertyName)) {
            throw new \Exception('Property ' . $propertyName . ' does not exist in ' . static::class);
        }

        return true;
    }
}
