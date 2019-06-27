<?php

namespace Helpcrunch\Exception;

use Exception;
use Helpcrunch\Response\ErrorResponse;

abstract class HelpcrunchException extends Exception
{
    /**
     * @var string|null
     */
    protected $innerErrorCode;

    /**
     * @var int|null
     */
    protected $errorCode;

    public function __construct(string $message = '', int $code = 0, string $innerErrorCode = '')
    {
        parent::__construct($message, $code);

        $this->innerErrorCode = $innerErrorCode;
    }

    public function getExceptionsResponse(): ErrorResponse
    {
        return new ErrorResponse($this->getData(), $this->innerErrorCode, $this->code);
    }

    abstract public function getData();
}
