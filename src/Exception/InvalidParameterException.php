<?php

namespace Helpcrunch\Exception;

use Helpcrunch\Response\InnerErrorCodes;
use Symfony\Component\HttpFoundation\JsonResponse;

class InvalidParameterException extends HelpcrunchException
{
    const MESSAGE = 'Invalid parameter ';

    /**
     * @var string
     */
    private $parameterName = '';

    public function __construct(string $parameterName = '')
    {
        parent::__construct(
            self::MESSAGE . $parameterName,
            JsonResponse::HTTP_BAD_REQUEST,
            InnerErrorCodes::INVALID_PARAMETER
        );

        $this->parameterName = $parameterName;
    }

    public function getData(): string
    {
        return self::MESSAGE . $this->parameterName;
    }
}
