<?php

namespace App\Traits;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;

trait JsonSerializerTrait
{
    public function jsonSerialize(): array
    {
        $serializer = SerializerBuilder::create()
            ->setPropertyNamingStrategy(
                new SerializedNameAnnotationStrategy(
                    new IdenticalPropertyNamingStrategy()
                )
            )
            ->build();

        $context = new SerializationContext();
        $context->setSerializeNull(true);

        return $serializer->toArray($this, $context);
    }
}
