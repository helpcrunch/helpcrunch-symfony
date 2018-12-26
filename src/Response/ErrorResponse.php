<?php

namespace Helpcrunch\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorResponse extends JsonResponse {
    /**
     * @param string|array $errorData
     * @param int|null $status
     * @param int|null $innerErrorCode
     */
    public function __construct($errorData, int $status = self::HTTP_BAD_REQUEST, int $innerErrorCode = null)
    {
        $responseData = [
            'error' => $errorData,
        ];
        if ($innerErrorCode) {
            $responseData['code'] = $innerErrorCode;
        }

        parent::__construct($responseData, $status);
    }
}
