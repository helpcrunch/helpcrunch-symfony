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
