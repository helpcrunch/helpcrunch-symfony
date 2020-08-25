<?php

namespace Helpcrunch\Validator;

use DateTime;
use Helpcrunch\Entity\DateTimeFilteredInterface;
use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Exception\ValidationException;
use Helpcrunch\Service\EntityFieldsParserService;
use Helpcrunch\Traits\HelpcrunchServicesTrait;
use Helpcrunch\Validator\Constraints\UniqueValue;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Time;
use Symfony\Component\Validator\Constraints\DateTime as DateTimeRule;
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

    public function isValid(HelpcrunchEntity $entity, array $data): HelpcrunchEntity
    {
        $collector = new ValidationRulesCollector();
        $collector->collectRules($entity);

        $data = $this->validateRelations($entity, $collector->getEntitiesRelations(), $data);

        $data = $this->filterDateTimes($entity, $data);
        $validationRules = $collector->getValidationRules();

        $data = $this->checkDateTimeValues($validationRules, $data);
        $this->validateData($entity, $collector->getValidationRules(), $data);
        if (!count($this->errors)) {
            return $this->createEntity($entity, $data);
        }

        throw new ValidationException($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function validateRelations(HelpcrunchEntity $entity, array $relations, array $data): array
    {
        foreach ($relations as $key => $relation) {
            if ($entity->id && empty($data[$key])) {
                continue;
            }
            if (empty($relation['targetEntity'])) {
                continue;
            }

            $targetEntity = $this->getRelationIfExists(
                $relation['targetEntity'],
                $relation['nullable'] ?? true,
                $key,
                $data[$key] ?? null
            );

            if ($targetEntity) {
                $data[$key] = $targetEntity;
            }
        }

        return $data;
    }

    private function getRelationIfExists(
        string $targetEntityClass,
        bool $nullAble,
        string $targetEntityField,
        int $targetEntityId = null
    ) {
        $repository = $this->getEntityManager()->getRepository($targetEntityClass);

        if ($targetEntityId) {
            $targetEntity = $repository->find($targetEntityId);

            if ($targetEntity) {
                return $targetEntity;
            } elseif (!$targetEntity && !$nullAble) {
                $this->errors[$targetEntityField] = $targetEntityClass . ' does not exist';
            }
        } else {
            if (!$nullAble) {
                $this->errors[$targetEntityField] = $targetEntityClass . ' can not be null';
            }
        }

        return false;
    }

    private function validateData(HelpcrunchEntity $entity, array $rules, array $data): void
    {
        $validation = Validation::createValidator();
        foreach ($rules as $field => $validationRules) {
            if ($entity->id && empty($data[$field])) {
                continue;
            }
            if (empty($rules[$field])) {
                continue;
            }

            $validationRules = $this->checkUniqueValueOnUpdate($entity, $validationRules);
            $validationRules = $this->checkNotBlankConstraint($entity, $field, $validationRules);

            $violation = $validation->validate($data[$field] ?? null, $validationRules);
            $this->collectErrors($field, $violation);
        }
    }

    private function checkDateTimeValues(array $validationRules, array $data): array
    {
        foreach ($data as $key => $value) {
            if (empty($validationRules[$key]) || is_null($value)) {
                continue;
            }

            $rule = reset($validationRules[$key]);
            if (($rule instanceof DateTimeRule) || ($rule instanceof Time)) {
                if ($value instanceof DateTime) {
                    $date = $value;
                } elseif (is_int($value)) {
                    $date = new DateTime();
                    $date->setTimestamp($value);
                } else {
                    $date = DateTime::createFromFormat(EntityFieldsParserService::DATETIME_FORMAT, $value);
                    if (!$date) {
                        $date = new DateTime();
                    }
                }

                $data[$key] = $date;
            }
        }

        return $data;
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
            if (($rule instanceof NotBlank) && !empty($entity->$field)) {
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

    private function createEntity(HelpcrunchEntity $entity, array $data): HelpcrunchEntity
    {
        foreach ($data as $key => $value) {
            if (property_exists($entity, $key)) {
                $entity->$key = $value;
            }
        }

        return $entity;
    }

    private function filterDateTimes(HelpcrunchEntity $entity, array $data): array
    {
        if ($entity instanceof DateTimeFilteredInterface) {
            if (!empty($data['createdAt'])) {
                unset($data['createdAt']);
            }

            if (!empty($data['updatedAt'])) {
                unset($data['updatedAt']);
            }
        }

        return $data;
    }
}
