<?php

namespace Helpcrunch\Exception;

use Exception;
use Helpcrunch\ExceptionResponse\HelpcrunchExceptionResponse;

abstract class HelpcrunchException extends Exception
{
    abstract public function getExceptionsResponse(): HelpcrunchExceptionResponse;

    abstract public function getData();
}
