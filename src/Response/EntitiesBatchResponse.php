<?php

namespace Helpcrunch\Response;

use Helpcrunch\Entity\HelpcrunchEntity;

class EntitiesBatchResponse extends SuccessResponse {
    /**
     * @param HelpcrunchEntity[] $entities
     * @param string|null $message
     * @param int|null $status
     */
    public function __construct(array $entities, $message = null, int $status = self::HTTP_OK)
    {
        foreach ($entities as $key => $entity) {
            $entities[$key] = $this->serializeEntity($entity);
        }

        parent::__construct(['entities' => $entities], $message, $status);
    }
}
