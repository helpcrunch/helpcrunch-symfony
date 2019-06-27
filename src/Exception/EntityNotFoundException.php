<?php

namespace Helpcrunch\Exception;

use Helpcrunch\ExceptionResponse\InvalidParameterExceptionResponse;
use Helpcrunch\ExceptionResponse\HelpcrunchExceptionResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class EntityNotFoundException extends HelpcrunchException
{
    const MESSAGE = 'Entity not found ';

    /**
     * @var string
     */
    private $entityName = '';

    public function __construct(string $entityName = '')
    {
        parent::__construct(self::MESSAGE . $entityName, JsonResponse::HTTP_NOT_FOUND);

        $this->entityName = $entityName;
    }

    public function getExceptionsResponse(): HelpcrunchExceptionResponse
    {
        return new InvalidParameterExceptionResponse($this);
    }

    public function getData(): string
    {
        return self::MESSAGE . $this->entityName;
    }
}
