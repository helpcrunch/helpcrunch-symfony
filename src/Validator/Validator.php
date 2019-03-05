<?php

namespace Helpcrunch\Validator;

use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Helper\ParametersValidatorHelper;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Annotation;
use Symfony\Component\Validator\Constraint;
use Doctrine\Common\Annotations\AnnotationException;

final class Validator
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var array
     */
    protected $validators = [];

    /**
     * @param HelpcrunchEntity $entity
     * @param array $data
     * @return bool|HelpcrunchEntity
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    public function isValid(HelpcrunchEntity $entity, array $data)
    {
        $validation = Validation::createValidator();

        $this->collectValidationRules($entity);
        foreach ($this->validators as $field => $validationRule) {
            if ($entity->id && empty($data[$field])) {
                continue;
            }

            $violation = $validation->validate($data[$field] ?? null, $this->validators[$field]);
            $this->collectErrors($field, $violation);
        }

        if (!count($this->errors)) {
            return $this->createEntity($entity, $data);
        }

        return false;
    }

    /**
     * @param HelpcrunchEntity $entity
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    private function collectValidationRules(HelpcrunchEntity $entity): void
    {
        $reader = new AnnotationReader();

        $reflectionClass = new \ReflectionClass($entity);
        foreach ($reflectionClass->getProperties() as $property) {
            $annotations = $reader->getPropertyAnnotations($property);
            foreach ($annotations as $annotation) {
                if (!ParametersValidatorHelper::isObject($annotation)) {
                    continue;
                }

                $reflectedAnnotation = new \ReflectionClass($annotation);
                if ($this->implementsDoctrineAnnotation($reflectedAnnotation)) {
                    continue;
                }

                if ($this->isSubClassOfConstraint($reflectedAnnotation)) {
                    $validatorParameters = get_object_vars($annotation);
                    try {
                        /** @var Constraint $validator */
                        $validator = $reflectedAnnotation->newInstance($validatorParameters);

                        $this->validators[$property->getName()][] = $validator;
                    } catch (\Throwable $exception) {
                        continue;
                    }
                }
            }
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function implementsDoctrineAnnotation(\ReflectionClass $class): bool
    {
        return $class->implementsInterface(Annotation::class);
    }

    private function isSubClassOfConstraint(\ReflectionClass $class): bool
    {
        return $class->isSubclassOf(Constraint::class);
    }

    protected function collectErrors($name, ConstraintViolationListInterface $violation): void
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
}
