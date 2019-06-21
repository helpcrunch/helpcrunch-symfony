<?php

namespace Helpcrunch\Exception;

use Helpcrunch\ExceptionResponse\HelpcrunchExceptionResponse;
use Helpcrunch\ExceptionResponse\ValidationExceptionResponse;

class ValidationException extends HelpcrunchException
{
    CONST MESSAGE = 'Validation error';
    const CODE = 400;

    /**
     * @var array
     */
    private $validationErrors;

    public function __construct(array $errors = [])
    {
        parent::__construct(self::MESSAGE, self::CODE);

        $this->validationErrors = $errors;
    }

    public function getExceptionsResponse(): HelpcrunchExceptionResponse
    {
        return new ValidationExceptionResponse($this);
    }

    public function getData(): array
    {
        return $this->validationErrors;
    }
}
