<?php

namespace Helpcrunch\Response;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class SuccessResponse extends HelpcrunchResponse
{
    public function __construct($data = [], $message = null, int $status = self::HTTP_OK)
    {
        $responseData['data'] = $data;
        if ($message) {
            $responseData['message'] = $this->getMessage($message);
        }
        $responseData['success'] = true;

        parent::__construct($this->serialize($responseData), $status);
    }

    protected function serialize(array $responseData): array
    {
        /** @var Serializer $serializer */
        $serializer = SerializerBuilder::create()
            ->setPropertyNamingStrategy(
                new SerializedNameAnnotationStrategy(
                    new IdenticalPropertyNamingStrategy()
                )
            )
            ->build();

        $context = new SerializationContext();
        $context->setSerializeNull(true);

        return $serializer->toArray($responseData, $context);
    }
}
