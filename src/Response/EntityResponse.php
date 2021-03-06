<?php

namespace Helpcrunch\Response;

use Helpcrunch\Entity\HelpcrunchEntity;

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
        $this->entity = $entity;
        if (is_object($entity) && method_exists($entity, 'jsonSerialize')) {
            $entity = $entity->jsonSerialize();
        }

        parent::__construct($entity, $message, $status);
    }

    /**
     * @return array|HelpcrunchEntity|null
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
