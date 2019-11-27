<?php

namespace Helpcrunch\Response;

class ErrorResponse extends HelpcrunchResponse
{
    const DEFAULT_ERROR_HEADERS = [
        'Access-Control-Allow-Origin' => '*'
    ];

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
}
