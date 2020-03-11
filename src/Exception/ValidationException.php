<?php

namespace Helpcrunch\Exception;

use Helpcrunch\Response\InnerErrorCodes;
use Symfony\Component\HttpFoundation\JsonResponse;

class ValidationException extends HelpcrunchException
{
    CONST MESSAGE = 'Validation error';

    /**
     * @var array
     */
    private $validationErrors;

    public function __construct(array $errors = [])
    {
        parent::__construct(
            self::MESSAGE,
            JsonResponse::HTTP_BAD_REQUEST,
            InnerErrorCodes::POST_ENTITY_VALIDATION_FAILED
        );

        $this->validationErrors = $errors;
    }

    public function getData(): array
    {
        return array_merge(['info' => self::MESSAGE], $this->validationErrors);
    }
}
