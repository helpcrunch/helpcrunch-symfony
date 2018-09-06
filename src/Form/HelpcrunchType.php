<?php

namespace Helpcrunch\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HelpcrunchType extends AbstractType
{
    /**
     * @var bool
     */
    protected $isNewEntity = true;

    public function setIsNewEntity(bool $isNewEntity): self
    {
        $this->isNewEntity = $isNewEntity;

        return $this;
    }
}
