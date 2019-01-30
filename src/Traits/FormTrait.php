<?php

namespace Helpcrunch\Traits;

use Helpcrunch\Entity\HelpcrunchEntity;
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

    protected function checkDataIsValid(array $data, FormInterface $form): array
    {
        $form->submit($data, false);
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

    protected function getForm(HelpcrunchEntity $entity, int $entityId = null): FormInterface
    {
        return $this->container->get('form.factory')->create($entity->getFormType(), $entity, ['entity_id' => $entityId]);
    }

    protected function createNewForm(HelpcrunchEntity $entity): FormInterface
    {
        return $this->getForm($entity, $entity->id);
    }
}
