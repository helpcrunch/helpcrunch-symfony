<?php

namespace Helpcrunch\Form;

use Symfony\Component\Form\AbstractType;

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
