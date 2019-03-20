<?php

namespace Helpcrunch\Helper;

use \Raven_Client;

class SentryHelper
{
    /**
     * @var \Raven_Client
     */
    private static $ravenClient;

    public static function install(string $ravenUrl = null): void
    {
        if (!$ravenUrl) {
            if (!empty($_SERVER['RAVEN_URL'])) {
                $ravenUrl = $_SERVER['RAVEN_URL'];
            } else {
                throw new \InvalidArgumentException('No raven url provided');
            }
        }

        self::$ravenClient = new Raven_Client($ravenUrl);
        self::$ravenClient->install($ravenUrl);
    }

    public static function log($message): void
    {
        if (!self::$ravenClient) {
            return;
        }

        if (is_array($message) || is_object($message)) {
            $message = json_encode($message);
        }
        self::$ravenClient->captureMessage($message, ['log'], ['level' => \Raven_Client::DEBUG]);
    }

    public static function logException($exception): void
    {
        if (!self::$ravenClient) {
            return;
        }

        self::$ravenClient->captureException($exception);
    }
}
