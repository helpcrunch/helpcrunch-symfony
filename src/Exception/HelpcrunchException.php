<?php

namespace Helpcrunch\Exception;

use Exception;
use Helpcrunch\ExceptionResponse\HelpcrunchExceptionResponse;
use Helpcrunch\Response\ErrorResponse;

abstract class HelpcrunchException extends Exception
{
    public function getExceptionsResponse(string $innerErrorCode = null, int $errorCode = null): ErrorResponse
    {
        return new ErrorResponse($this->getData(), $innerErrorCode, $errorCode);
    }

    abstract public function getData();
}
