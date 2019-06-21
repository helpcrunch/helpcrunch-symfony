<?php

namespace Helpcrunch\ExceptionResponse;

use Helpcrunch\Exception\HelpcrunchException;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class HelpcrunchExceptionResponse
{
    /**
     * @var HelpcrunchException
     */
    protected $exception;

    public function __construct(HelpcrunchException $exception)
    {
        $this->exception = $exception;
    }

    abstract public function createResponse(): JsonResponse;
}
