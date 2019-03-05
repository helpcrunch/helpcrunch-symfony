<?php

namespace Helpcrunch\Validator;

use Doctrine\ORM\Mapping\Annotation;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;
use Helpcrunch\Validator\ValidationRule\AssertionRule;
use Helpcrunch\Validator\ValidationRule\JoinColumnRule;
use Helpcrunch\Validator\ValidationRule\RelationRule;
use Helpcrunch\Validator\ValidationRule\ValidationRuleInterface;
use Symfony\Component\Validator\Constraint;

class ValidationRulesFactory
{
    const DOCTRINE_RELATION_CLASSES = [
        ManyToOne::class,
        ManyToMany::class,
        OneToOne::class,
    ];

    /**
     * @param \ReflectionClass $class
     * @return bool|ValidationRuleInterface
     */
    public static function getValidationRulesCollector(\ReflectionClass $class)
    {
        if (self::implementsDoctrineRelation($class)) {
            return new RelationRule();
        }

        if (self::implementsJoinColumn($class)) {
            return new JoinColumnRule();
        }

        if (self::isSubClassOfConstraint($class)) {
            return new AssertionRule();
        }

        return false;
    }

    private static function implementsJoinColumn(\ReflectionClass $class): bool
    {
        return ($class->getName() == JoinColumn::class);
    }

    private static function implementsDoctrineRelation(\ReflectionClass $class): bool
    {
        return $class->implementsInterface(Annotation::class) &&
            $class->isFinal() &&
            in_array($class->getName(), self::DOCTRINE_RELATION_CLASSES);
    }

    private static function isSubClassOfConstraint(\ReflectionClass $class): bool
    {
        return $class->isSubclassOf(Constraint::class);
    }
}
