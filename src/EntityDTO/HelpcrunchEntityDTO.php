<?php

namespace Helpcrunch\EntityDTO;

use Helpcrunch\Entity\GetterSetterTrait;

class HelpcrunchEntityDTO
{
    use GetterSetterTrait;

    public function toArray(): array
    {
        $arrayEntity = [];
        foreach ($this as $attribute => $value) {
            $arrayEntity[$attribute] = $value;
        }

        return $arrayEntity;
    }
}
