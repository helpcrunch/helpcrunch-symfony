<?php

namespace Helpcrunch\Response;

use Helpcrunch\Entity\HelpcrunchEntity;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

class SuccessResponse extends JsonResponse
{
    public function __construct(array $data = [], $message = null, int $status = self::HTTP_OK)
    {
        $responseData = $data;
        if ($message) {
            $responseData['message'] = $message;
        }
        $responseData['success'] = true;

        parent::__construct($this->serialize($responseData), $status);
    }

    /**
     * @param mixed[]|HelpcrunchEntity|HelpcrunchEntity[] $entity
     * @return array
     */
    protected function serialize($entity): array
    {
        /** @var Serializer $serializer */
        $serializer = SerializerBuilder::create()
            ->setPropertyNamingStrategy(
                new SerializedNameAnnotationStrategy(
                    new IdenticalPropertyNamingStrategy()
                )
            )
            ->build();

        return $serializer->toArray($entity);
    }
}
