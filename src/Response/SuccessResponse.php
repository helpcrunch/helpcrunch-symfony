<?php

namespace Helpcrunch\Response;

use Helpcrunch\Traits\JsonSerializerTrait;

class SuccessResponse extends HelpcrunchResponse
{
    use JsonSerializerTrait;

    public function __construct($data = null, $message = null, int $status = self::HTTP_OK)
    {
        $responseData['data'] = $data;
        if ($message) {
            $responseData['message'] = $this->getMessage($message);
        }

        parent::__construct($this->getSerializedData($responseData), $status);
    }

    protected function getSerializedData(array $responseData): array
    {
        return $this->createSerializer()->toArray($responseData, $this->getSerializationContext());
    }
}
