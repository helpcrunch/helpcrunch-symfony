<?php

namespace Helpcrunch\Annotation\AuthSpecification;

use Helpcrunch\Annotation\AuthSpecificationInterface;

class AbstractAuthSpecification implements AuthSpecificationInterface
{
    /**
     * @var AuthSpecificationInterface[]
     */
    private $authorizationsSpecifications;

    public function __construct(array $authorizationsSpecifications)
    {
        $this->authorizationsSpecifications = $authorizationsSpecifications;
    }

    public function checkPermission(): bool
    {
        foreach ($this->authorizationsSpecifications as $specification) {
            if (get_class($specification) == static::class) {
                return true;
            }
        }

        return false;
    }
}
