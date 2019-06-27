<?php

namespace Helpcrunch\ExceptionResponse;

use Helpcrunch\Exception\EntityNotFoundException;
use Helpcrunch\Response\ErrorResponse;
use Helpcrunch\Response\InnerErrorCodes;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @property EntityNotFoundException $exception
 */
class EntityNotFoundExceptionResponse extends HelpcrunchExceptionResponse
{
    public function createResponse(): JsonResponse
    {
        return new ErrorResponse($this->exception->getData(), InnerErrorCodes::ENTITY_NOT_FOUND);
    }
}
