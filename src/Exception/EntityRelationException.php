<?php

namespace Helpcrunch\Exception;

use Helpcrunch\Response\InnerErrorCodes;
use Symfony\Component\HttpFoundation\JsonResponse;

class EntityRelationException extends HelpcrunchException
{
    const MESSAGE = 'Entity %belong does not belong to entity %entity';

    /**
     * @var string
     */
    private $entityName = '';

    /**
     * @var string
     */
    private $belongEntityName = '';

    public function __construct(string $entityName = '', string $belongEntityName = '')
    {
        $this->entityName = $entityName;
        $this->belongEntityName = $belongEntityName;

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
        $message = str_replace('%belong', $this->belongEntityName, self::MESSAGE);

        return str_replace('%entity', $this->entityName, $message);
    }
}
