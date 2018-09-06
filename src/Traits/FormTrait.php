<?php

namespace Helpcrunch\Traits;

use Helpcrunch\Type\HelpcrunchType;
use Symfony\Component\Form\FormInterface;

trait FormTrait
{
    protected function getErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $child) {
            $errors[$child->getOrigin()->getName()] = $child->getMessage();
        }

        return $errors;
    }

    protected function checkDataIsValid(array $data, HelpcrunchType $form, bool $isNewEntity = true): array
    {
        $form->setIsNewEntity($isNewEntity);
        $form->submit($data);
        if (!$form->isValid()) {
            return [
                'valid' => false,
                'errors' => $this->getErrors($form),
            ];
        }

        return [
            'valid' => true,
            'entity' => $form->getData(),
        ];
    }
}
