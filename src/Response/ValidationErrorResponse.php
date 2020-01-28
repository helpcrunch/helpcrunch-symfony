<?php

namespace Helpcrunch\Response;

use Helpcrunch\Exception\ValidationException;

class ValidationErrorResponse extends ErrorResponse
{
    protected static $defaultErrorMessageInfo = ValidationException::MESSAGE;
}
