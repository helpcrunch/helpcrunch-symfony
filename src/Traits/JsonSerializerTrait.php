<?php

namespace Helpcrunch\Traits;

use App\Serializer\ExcludePolicyFunctionsProvider;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

trait JsonSerializerTrait
{
    public function jsonSerialize(): array
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        return $this->createSerializer()->toArray($this, $context);
    }

    protected function createSerializer(): Serializer
    {
        return SerializerBuilder::create()
            ->setPropertyNamingStrategy(
                new SerializedNameAnnotationStrategy(
                    new IdenticalPropertyNamingStrategy()
                )
            )
            ->setExpressionEvaluator(new ExpressionEvaluator($this->createAuthenticatedAsDeviceEvaluator()))
            ->build();
    }

    protected function createAuthenticatedAsDeviceEvaluator(): ExpressionLanguage
    {
        $language = new ExpressionLanguage();
        $language->registerProvider(new ExcludePolicyFunctionsProvider());

        return $language;
    }
}
