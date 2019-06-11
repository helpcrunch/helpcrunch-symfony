<?php

namespace Helpcrunch\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class SuccessResponse extends JsonResponse
{
    public function __construct($data = [], $message = null, int $status = self::HTTP_OK)
    {
        if (!is_array($data)) {
            $data = ['data' => $data];
        }
        $responseData = $data;
        if ($message) {
            $responseData['message'] = $message;
        }
        $responseData['success'] = true;

        parent::__construct($responseData, $status);
    }
}
