<?php

namespace Helpcrunch\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

abstract class HelpcrunchResponse extends JsonResponse
{
    protected static $defaultErrorMessageInfo = 'Something went wrong';

    /**
     * @param array|string $clientMessage
     * @return array
     */
    protected function getMessage($clientMessage): array
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
