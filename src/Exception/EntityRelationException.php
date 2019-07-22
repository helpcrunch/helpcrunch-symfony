<?php

namespace Helpcrunch\Exception;

use Helpcrunch\Response\InnerErrorCodes;
use Symfony\Component\HttpFoundation\JsonResponse;

class EntityRelationException extends HelpcrunchException
{
    const MESSAGE = 'Entity %parent does not belong to entity %entity';

    /**
     * @var string
     */
    private $parentEntity = '';

    /**
     * @var string
     */
    private $childEntity = '';

    public function __construct(string $parentEntity = '', string $childEntity = '')
    {
        $this->parentEntity = $parentEntity;
        $this->childEntity = $childEntity;

        parent::__construct(
            $this->createMessage(),
            JsonResponse::HTTP_NOT_ACCEPTABLE,
            InnerErrorCodes::PARENT_ENTITIES_MISMATCH
        );
    }

    public function getData(): string
    {
        return $this->createMessage();
    }

    private function createMessage(): string
    {
        $message = str_replace('%child', $this->childEntity, self::MESSAGE);

        return str_replace('%parent', $this->parentEntity, $message);
    }
}
