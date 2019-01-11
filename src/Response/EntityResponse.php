<?php

namespace Helpcrunch\Response;

use Helpcrunch\Entity\HelpcrunchEntity;

class EntityResponse extends SuccessResponse
{
    /**
     * @var HelpcrunchEntity
     */
    private $entity;

    public function __construct(HelpcrunchEntity $entity = null, $message = null, int $status = self::HTTP_OK)
    {
        $this->entity = $entity;
        if (is_object($entity)) {
            $entity = $this->serializeEntity($entity);
        }
        parent::__construct(['data' => $entity], $message, $status);
    }

    public function getEntity(): HelpcrunchEntity
    {
        return $this->entity;
    }
}
