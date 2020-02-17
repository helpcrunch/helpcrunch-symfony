<?php

namespace Helpcrunch\Validator;

use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\EntityDTO\HelpcrunchEntityDTO;
use Helpcrunch\Exception\ValidationException;
use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Helpcrunch\Validator\Constraints\UniqueValue;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

final class Validator
{
    use HelpcrunchServicesTrait;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $errors = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function isValid(HelpcrunchEntity $entity, HelpcrunchEntityDTO $entityDTO): void
    {
        $collector = new ValidationRulesCollector();
        $collector->collectRules($entity);

        $this->validateRelations($entity, $entityDTO, $collector->getEntitiesRelations());
        $this->validateData($entity, $entityDTO, $collector->getValidationRules());

        if (count($this->errors)) {
            throw new ValidationException($this->errors);
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function validateRelations(HelpcrunchEntity $entity, HelpcrunchEntityDTO $entityDTO, array $relations): void
    {
        foreach ($relations as $key => $relation) {
            $dtoValue = $entityDTO->{$key};
            if ($entity->id && empty($dtoValue)) {
                continue;
            }
            if (empty($relation['targetEntity'])) {
                continue;
            }

            $this->checkRelationIfExists(
                $relation['targetEntity'],
                $relation['nullable'] ?? true,
                $key,
                $entityDTO->{$key} ?? null
            );
        }
    }

    private function checkRelationIfExists(
        string $targetEntityClass,
        bool $nullAble,
        string $targetEntityField,
        HelpcrunchEntity $entity = null
    ): void {
        if ($nullAble) {
            return;
        }

        if (!$entity) {
            $this->errors[$targetEntityField] = $targetEntityClass . ' can not be null';
        }

        if (!($entity instanceof $targetEntityClass)) {
            $this->errors[$targetEntityField] = $targetEntityClass . ' does not exist';
        }
    }

    private function validateData(HelpcrunchEntity $entity, HelpcrunchEntityDTO $entityDTO, array $rules): void
    {
        $validation = Validation::createValidator();
        foreach ($rules as $field => $validationRules) {
            $dtoValue = $entityDTO->{$field};
            if ($entity->id && empty($dtoValue)) {
                continue;
            }
            if (empty($rules[$field])) {
                continue;
            }

            $validationRules = $this->checkUniqueValueOnUpdate($entity, $validationRules);
            $validationRules = $this->checkNotBlankConstraint($entity, $field, $validationRules);

            $violation = $validation->validate($entityDTO->{$field} ?? null, $validationRules);
            $this->collectErrors($field, $violation);
        }
    }

    private function checkUniqueValueOnUpdate(HelpcrunchEntity $entity, array $constraints): array
    {
        foreach ($constraints as $key => $constraint) {
            if ($entity->id && ($constraint instanceof UniqueValue)) {
                unset($constraints[$key]);
            }
        }

        return $constraints;
    }

    private function checkNotBlankConstraint(HelpcrunchEntity $entity, string $field, array $constraints): array
    {
        foreach ($constraints as $key => $rule) {
            $value = $entity->{$field};
            if (($rule instanceof NotBlank) && !empty($value)) {
                unset($constraints[$key]);
            }
        }

        return $constraints;
    }

    private function collectErrors($name, ConstraintViolationListInterface $violation): void
    {
        if ($violation->count()) {
            $this->errors[$name] = $violation[0]->getMessage();
        }
    }
}
