<?php

namespace Helpcrunch\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @property int $id
 */
abstract class HelpcrunchEntity
{
    use GetterSetterTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    abstract public function getFormType(): string;
}
