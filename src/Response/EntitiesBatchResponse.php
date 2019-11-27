<?php

namespace Helpcrunch\Response;

use Helpcrunch\Entity\HelpcrunchEntity;
use JsonSerializable;

class EntitiesBatchResponse extends SuccessResponse
{
    /**
     * @param HelpcrunchEntity[] $entities
     * @param string|null $message
     * @param int|null $status
     */
    public function __construct(array $entities, $message = null, int $status = self::HTTP_OK)
    {
        $serializedEntities = [];
        foreach ($entities as $entity) {
            if (is_object($entity) && method_exists($entity, 'jsonSerialize')) {
                $serializedEntities[] = $entity->jsonSerialize();
            } else {
                $serializedEntities = $entity;
            }
        }

        parent::__construct($serializedEntities, $message, $status);
    }
}
