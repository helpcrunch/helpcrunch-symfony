<?php

namespace Helpcrunch\Serializer\Accessor;

use JMS\Serializer\Accessor\AccessorStrategyInterface;
use JMS\Serializer\Metadata\PropertyMetadata;

class HelpcrunchAccessorStrategy implements AccessorStrategyInterface
{
    /**
     * @param object $object
     * @param PropertyMetadata $metadata
     * @return mixed
     */
    public function getValue($object, PropertyMetadata $metadata)
    {
        if (null === $metadata->getter) {
            return $metadata->getValue($object);
        }

        return $object->{$metadata->getter}($metadata->name);
    }

    /**
     * @param object $object
     * @param mixed $value
     * @param PropertyMetadata $metadata
     */
    public function setValue($object, $value, PropertyMetadata $metadata)
    {
        if (null === $metadata->setter) {
            $metadata->setValue($object, $value);
        }

        $object->{$metadata->setter}($metadata->name, $value);
    }
}
