<?php

namespace Helpcrunch\Response;

use Helpcrunch\Entity\HelpcrunchEntity;
use JsonSerializable;

class EntityResponse extends SuccessResponse
{
    /**
     * @var array|HelpcrunchEntity|null
     */
    private $entity;

    /**
     * @param HelpcrunchEntity|array|null $entity
     * @param string|null $message
     * @param int $status
     */
    public function __construct($entity = null, $message = null, int $status = self::HTTP_OK)
    {
        if (is_object($entity) && ($entity instanceof JsonSerializable)) {
            $entity = $entity->jsonSerialize();
        }

        parent::__construct(['data' => $entity], $message, $status);
    }

    /**
     * @return array|HelpcrunchEntity|null
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
