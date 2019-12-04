<?php

namespace Helpcrunch\Traits;

use Helpcrunch\Serializer\ExcludePolicyFunctionsProvider;
use Helpcrunch\Serializer\Accessor\HelpcrunchAccessorStrategy;
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
        return $this->createSerializer()->toArray($this, $this->getSerializationContext());
    }

    protected function createSerializer(): Serializer
    {
        $serializerBuilder = SerializerBuilder::create()
            ->setPropertyNamingStrategy(
                new SerializedNameAnnotationStrategy(
                    new IdenticalPropertyNamingStrategy()
                )
            )
            ->setExpressionEvaluator(new ExpressionEvaluator($this->createAuthenticatedEvaluator()));

        $serializerBuilder->setAccessorStrategy(new HelpcrunchAccessorStrategy());

        return $serializerBuilder->build();
    }

    protected function getSerializationContext(): SerializationContext
    {
        $serializationContext = new SerializationContext();
        $serializationContext->setSerializeNull(true);

        return $serializationContext;
    }

    protected function createAuthenticatedEvaluator(): ExpressionLanguage
    {
        $expressionLanguage = new ExpressionLanguage();
        $expressionLanguage->registerProvider(new ExcludePolicyFunctionsProvider());

        return $expressionLanguage;
    }
}
