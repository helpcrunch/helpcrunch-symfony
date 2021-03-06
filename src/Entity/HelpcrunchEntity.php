<?php

namespace Helpcrunch\Entity;

use Helpcrunch\Traits\JsonSerializerTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @property int $id
 */
abstract class HelpcrunchEntity
{
    use GetterSetterTrait, JsonSerializerTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;
}
