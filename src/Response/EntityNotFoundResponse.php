<?php

namespace Helpcrunch\Response;

class EntityNotFoundResponse extends ErrorResponse
{
    public function __construct(string $entityName)
    {
        parent::__construct(
            ucfirst($entityName) . ' not found',
            InnerErrorCodes::ENTITY_NOT_FOUND,
            self::HTTP_NOT_FOUND
        );
    }
}
