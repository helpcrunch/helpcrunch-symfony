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
        if ($this->getterExists($name)) {
            return $this->executeGetter($name);
        } elseif ($this->checkPropertyExists($name)) {
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
        if ($this->setterExists($name)) {
            $this->executeSetter($name, $value);
        } elseif ($this->checkPropertyExists($name)) {
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

    protected function getterExists(string $name): bool
    {
        return method_exists($this, $this->getMethodName('get', $name));
    }

    protected function setterExists(string $name): bool
    {
        return method_exists($this, $this->getMethodName('set', $name));
    }

    protected function executeGetter(string $name)
    {
        $methodName = $this->getMethodName('get', $name);

        return $this->$methodName();
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this|null
     */
    protected function executeSetter(string $name, $value)
    {
        $methodName = $this->getMethodName('set', $name);

        return $this->$methodName($value);
    }

    protected function getMethodName(string $methodType, string $name): string
    {
        return $methodType . strtoupper(substr($name, 0, 1)) . substr($name, 1);
    }
}
