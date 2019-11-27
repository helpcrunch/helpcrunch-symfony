<?php

namespace Helpcrunch\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorResponse extends JsonResponse
{
    const DEFAULT_ERROR_HEADERS = [
        'Access-Control-Allow-Origin' => '*'
    ];

    protected static $defaultErrorMessageInfo = 'Something went wrong';

    public function __construct($message, string $innerErrorCode = null, int $status = self::HTTP_BAD_REQUEST, $errors = [])
    {
        $responseData = [
            'message' => $this->getMessage($message),
            'success' => false,
        ];
        if ($innerErrorCode) {
            $responseData['code'] = $innerErrorCode;
        }
        if ($errors) {
            $responseData['errors'] = $errors;
        }

        parent::__construct($responseData, $status, self::DEFAULT_ERROR_HEADERS);
    }

    /**
     * @param array|string $clientMessage
     * @return array
     */
    private function getMessage($clientMessage): array
    {
        $message = ['info' => $this->getMessageInfo($clientMessage)];
        if (is_array($clientMessage)) {
            $message = array_merge($message, $clientMessage);
        }

        return $message;
    }

    /**
     * @param array|string $clientMessage
     * @return string
     */
    private function getMessageInfo($clientMessage): string
    {
        return is_string($clientMessage) ? $clientMessage : static::$defaultErrorMessageInfo;
    }
}
