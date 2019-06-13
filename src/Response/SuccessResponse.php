<?php

namespace Helpcrunch\Response;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

class SuccessResponse extends JsonResponse
{
    public function __construct($data = [], $message = null, int $status = self::HTTP_OK)
    {
        if (!is_array($data)) {
            $data = ['data' => $data];
        }
        $responseData = $data;
        if ($message) {
            $responseData['message'] = $message;
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
