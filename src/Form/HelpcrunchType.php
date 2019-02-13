<?php

namespace Helpcrunch\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HelpcrunchType extends AbstractType
{
    /**
     * @var string|null
     */
    protected static $entityClass;

    protected function getDefaultOptions(): array
    {
        return [
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'entity_id' => false,
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        if (!empty(static::$entityClass)) {
            $defaultOptions = $this->getDefaultOptions();
            $resolver->setDefaults(array_merge(['data_class' => static::$entityClass], $defaultOptions));
        }
    }
}
