<?php

namespace Helpcrunch\Exception;

use Helpcrunch\ExceptionResponse\InvalidParameterExceptionResponse;
use Helpcrunch\ExceptionResponse\HelpcrunchExceptionResponse;

class InvalidParameterException extends HelpcrunchException
{
    const MESSAGE = 'Invalid parameter ';
    const CODE = 400;

    /**
     * @var string
     */
    private $parameterName = '';

    public function __construct(string $parameterName = '')
    {
        parent::__construct(self::MESSAGE . $parameterName, self::CODE);

        $this->parameterName = $parameterName;
    }

    public function getExceptionsResponse(): HelpcrunchExceptionResponse
    {
        return new InvalidParameterExceptionResponse($this);
    }

    public function getData(): string
    {
        return self::MESSAGE . $this->parameterName;
    }
}
