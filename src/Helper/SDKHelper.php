<?php declare(strict_types=1);

namespace Helpcrunch\Helper;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

class SDKHelper
{
    private const SDK_VERSION_HEADER_KEY = 'SDK-Version';
    private const SDK_LIGHT_RESPONSE_HEADER_KEY = 'SDK-Light-Response';

    /**
     * @var null|HeaderBag
     */
    private static $headers = null;

    public static function initialize(Request $request): void
    {
        self::$headers = $request->headers;
    }

    public static function isSdk(): bool
    {
        return !empty(self::getHeader(self::SDK_VERSION_HEADER_KEY));
    }

    public static function isLightResponse(): bool
    {
        return self::isSdk() && (self::getHeader(self::SDK_LIGHT_RESPONSE_HEADER_KEY) === 'true');
    }

    private static function getHeader(string $key): ?string
    {
        if (self::$headers == null) {
            return null;
        }

        return self::$headers->get($key);
    }
}
