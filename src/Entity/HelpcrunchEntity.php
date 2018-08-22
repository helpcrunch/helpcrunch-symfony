<?php

namespace Helpcrunch\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class HelpcrunchEntity
{
    use GetterSetterTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    abstract public function getFormType(): string;

    abstract protected function generateToken(): string;
}
