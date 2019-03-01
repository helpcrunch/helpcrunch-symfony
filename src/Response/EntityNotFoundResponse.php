<?php

namespace Helpcrunch\Response;

class EntityNotFoundResponse extends ErrorResponse
{
    public function __construct(string $entityName)
    {
        parent::__construct(
            ucfirst($entityName) . ' not found',
            InnerErrorCodes::ENTITY_DOES_NOT_EXIST,
            self::HTTP_NOT_FOUND
        );
    }
}
