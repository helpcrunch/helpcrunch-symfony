<?php

namespace Helpcrunch\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorResponse extends JsonResponse {
    public function __construct(string $message, int $innerErrorCode = null, $errors = [], int $status = self::HTTP_BAD_REQUEST)
    {
        $responseData = [
            'message' => $message,
            'success' => false,
        ];
        if ($innerErrorCode) {
            $responseData['code'] = $innerErrorCode;
        }
        if ($errors) {
            $responseData['errors'] = $errors;
        }

        parent::__construct($responseData, $status);
    }
}
