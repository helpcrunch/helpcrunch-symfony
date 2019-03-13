<?php

namespace Helpcrunch\Validator;

use Helpcrunch\Entity\HelpcrunchEntity;
use Helpcrunch\Helper\ParametersValidatorHelper;
use Doctrine\Common\Annotations\AnnotationReader;
use Helpcrunch\Validator\ValidationRule\AssertionRule;
use Doctrine\Common\Annotations\AnnotationException;

class ValidationRulesCollector
{
    /**
     * @var array
     */
    private $entitiesRelations = [];

    /**
     * @var array
     */
    private $validationRules = [];

    /**
     * @param HelpcrunchEntity $entity
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    public function collectRules(HelpcrunchEntity $entity): void
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

                $collector = ValidationRulesFactory::getValidationRulesCollector($reflectedAnnotation);
                if (!$collector) {
                    continue;
                }

                if ($collector instanceof AssertionRule) {
                    $rule = $collector->getRule($reflectedAnnotation, $annotation);
                    $this->validationRules[$property->getName()][$this->getAssertionRuleName($reflectedAnnotation)] = $rule;
                } else {
                    $this->entitiesRelations[$property->getName()][$collector::RULE_VARIABLE] = $collector->getRule($reflectedAnnotation, $annotation);
                }
            }
        }
    }

    private function getAssertionRuleName(\ReflectionClass $reflectionClass): string
    {
        $constraintClassNameParts = explode('\\', $reflectionClass->getName());

        return end($constraintClassNameParts);
    }

    public function getEntitiesRelations(): array
    {
        return $this->entitiesRelations;
    }

    public function getValidationRules(): array
    {
        return $this->validationRules;
    }
}
