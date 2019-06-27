<?php

namespace Helpcrunch\ExceptionResponse;

use Helpcrunch\Exception\InvalidParameterException;
use Helpcrunch\Response\ErrorResponse;
use Helpcrunch\Response\InnerErrorCodes;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @property InvalidParameterException $exception
 */
class InvalidParameterExceptionResponse extends HelpcrunchExceptionResponse
{
    public function createResponse(): JsonResponse
    {
        return new ErrorResponse($this->exception->getData(), InnerErrorCodes::INVALID_PARAMETER);
    }
}
