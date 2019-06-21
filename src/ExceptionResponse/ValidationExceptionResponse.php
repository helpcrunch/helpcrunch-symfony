<?php

namespace Helpcrunch\ExceptionResponse;

use Helpcrunch\Exception\ValidationException;
use Helpcrunch\Response\ErrorResponse;
use Helpcrunch\Response\InnerErrorCodes;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @property ValidationException $exception
 */
class ValidationExceptionResponse extends HelpcrunchExceptionResponse
{
    public function createResponse(): JsonResponse
    {
        return new ErrorResponse($this->exception->getData(), InnerErrorCodes::POST_ENTITY_VALIDATION_FAILED);
    }
}
