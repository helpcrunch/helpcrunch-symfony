<?php

namespace Helpcrunch\Response;

use Helpcrunch\Entity\HelpcrunchEntity;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

class SuccessResponse extends JsonResponse
{
    public function __construct(array $data = [], $message = null, int $status = self::HTTP_OK)
    {
        $responseData = $data;
        if ($message) {
            $responseData = ['message' => $message];
        }
        $responseData['success'] = true;

        parent::__construct($responseData, $status);
    }

    protected function serializeEntity(HelpcrunchEntity $entity): array
    {
        /** @var Serializer $serializer */
        $serializer = SerializerBuilder::create()
            ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
            ->build();

        return $serializer->toArray($entity);
    }
}
