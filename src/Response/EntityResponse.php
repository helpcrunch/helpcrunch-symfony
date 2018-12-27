<?php

namespace Helpcrunch\Response;

use Helpcrunch\Entity\HelpcrunchEntity;

class EntityResponse extends SuccessResponse
{
    public function __construct(HelpcrunchEntity $entity = null, $message = null, int $status = self::HTTP_OK)
    {
        parent::__construct(['entity' => $this->serializeEntity($entity)], $message, $status);
    }
}
