<?php

namespace Helpcrunch\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HelpcrunchType extends AbstractType
{
    protected function getDefaultOptions(): array
    {
        return [
            'allow_extra_fields' => true,
            'csrf_protection' => false,
        ];
    }
}
